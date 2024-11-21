<?php

namespace App\Http\Controllers\Env\Sinv\Query;
use Illuminate\Support\Facades\DB;

class SinvQ
{
   public static function getTopRecords($fromDate, $toDate)
{
     $query = "
    WITH RankedRecords AS (
        SELECT *,  
               ROW_NUMBER() OVER (PARTITION BY t_bpid ORDER BY t_efdt DESC) AS rank
        FROM erp.dbo.ttctax400800 b WITH (NOLOCK)
    ),
    Address AS (
        SELECT 
            D.t_bpid AS BP, 
            b.t_fovn as TIN,
            D.t_beid as BRN,
            D.t_nama AS RegName, 
            A.t_cadr AS AdCode, 
            A.t_dsca AS City, 
            A.t_ccty AS Country, 
            D.t_telp AS Tel, 
            t_ln02  AS Add1, 
            t_ln03 AS Add2, 
            t_ln04 + t_ln05 AS Add3,
            A.t_nama as OTname,
            t_cste as StateCode
        FROM 
            erp.dbo.ttccom130800 A WITH (NOLOCK)
            LEFT JOIN erp.dbo.ttccom100800 D WITH (NOLOCK) ON D.t_cadr = A.t_cadr 
            LEFT JOIN RankedRecords b on D.t_bpid = b.t_bpid and rank = 1
        WHERE D.t_prst = 2
    ),
    OneTime AS(
        SELECT 
            t_cadr as AdCode,
            t_ln01 as OTname,
            t_dsca  as City,
            t_ccty as Country,
            t_telp as Tel,
            t_ln02  AS Add1,
            t_ln03  as Add2, 
            t_ln04 + t_ln05 AS Add3,
            t_cste as StateCode
        FROM  
            erp.dbo.ttccom130800 WITH (NOLOCK)
    ),
    InvoiceInfo AS (
        SELECT 
            'SInvoice' AS DocumentType, 
            '800' + '/' + CONVERT(VARCHAR(50), A.t_tran) + '/' + CONVERT(VARCHAR(50), A.t_idoc) AS InvoiceID, 
            --'Test-01' as InvoiceID,
            FORMAT(DATEADD(HOUR, 8, A.t_idat), 'MM/dd/yyyy HH:mm:ss') AS DocumentDate, 
            A.t_itbp AS CusCode, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN 'EI00000000010'
                WHEN CUS.Country != 'MYS' THEN 'EI00000000010'
                ELSE  CUS.TIN 
            END AS TIN,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN 'NA' 
                ELSE  CUS.BRN 
            END AS BRN,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.OTname 
                ELSE  CUS.RegName 
            END AS CusRegName,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Add1 
                ELSE   CUS.Add1 
            END AS CusAddress1,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Add2 
                ELSE  CUS.Add2 
            END AS CusAddress2,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Add3 
                ELSE CUS.Add3 
            END AS CusAddress3,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Country 
                ELSE CUS.Country 
            END AS Country, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.City 
                ELSE CUS.City 
            END AS City, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.StateCode  
                ELSE CUS.StateCode 
            END AS StateCode, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Tel 
                ELSE CUS.Tel 
            END AS Tel, 
            A.t_ccur AS Currency, 
            A.t_rate_1 AS CurrencyRate, 
            B.t_slsf AS SoRef, 
            C.t_corn as CusPo,
            B.t_shpf AS DoRef, 
            I.t_dsca AS Terms, 
            A.t_amti AS InvoiceTotalAmount, 
            '' AS PartNo, 
            CASE 
                WHEN ltrim(D.t_item) in ('Z05','Z07') THEN '027' 
                ELSE '022' 
            END as Classification,
            CASE 
                WHEN  A.t_tran = 'S02' THEN J.t_desc 
                ELSE G.t_dsca 
            END as ItemDescription,
            CASE 
                WHEN  A.t_tran = 'S01' THEN F.t_cuqs 
                ELSE J.t_cuni 
            END as OrderUOM,
            D.t_dqua AS [InvoiceQty], 
            CASE 
                WHEN F.t_cups = '/p2' THEN D.t_pric / 100 
                ELSE D.t_pric 
            END AS UnitPrice, 
            '06' AS TaxType,  
            0 AS TaxRate, 
            0 AS TaxAmount, 
            0 AS TaxPrice, 
            D.t_amti AS ItemAmt, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.OTname 
                ELSE   Ship.RegName  
            END AS ShipReceiptName,
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Add1 
                ELSE Ship.Add1 
            END AS ShipAddress1, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Add2 
                ELSE Ship.Add2 
            END AS ShipAddress2, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Add3 
                ELSE  Ship.Add3 
            END AS ShipAddress3, 
            CASE 
                WHEN A.t_itbp= 'OT0000001' THEN OneTime.Country 
                ELSE  Ship.Country 
            END AS ShipCountry,
            (DATEADD(HOUR, 8, A.t_idat)) as ddd,
            D.t_amth_1 as AmtInMyr
        FROM 
            erp.dbo.tcisli305800 A WITH (NOLOCK)
            LEFT JOIN erp.dbo.tcisli200800 B WITH (NOLOCK) ON B.t_brid = A.t_brid 
            LEFT JOIN erp.dbo.ttdsls400800 C WITH (NOLOCK) ON C.t_orno = B.t_slsf 
            LEFT JOIN erp.dbo.tcisli310800 D WITH (NOLOCK) ON D.t_idoc = A.t_idoc AND D.t_tran = A.t_tran 
            LEFT JOIN erp.dbo.ttdsls401800 F WITH (NOLOCK) ON F.t_orno = C.t_orno AND F.t_invn = A.t_idoc AND F.t_pono = D.t_pono 
            LEFT JOIN erp.dbo.ttcibd001800 G WITH (NOLOCK) ON G.t_item =  D.t_item 
            LEFT JOIN Address Ship WITH (NOLOCK) ON Ship.BP = D.t_stbp
            LEFT JOIN Address CUS WITH (NOLOCK) ON CUS.BP = D.t_ofbp
            LEFT JOIN erp.dbo.tcisli225800 J WITH (NOLOCK) ON A.t_msid = J.t_msid AND J.t_msln = D.t_pono
            LEFT JOIN erp.dbo.ttcmcs013800 I WITH (NOLOCK) ON I.t_cpay = A.t_cpay 
            LEFT JOIN erp.dbo.twhinh430800 DO WITH (NOLOCK) ON DO.t_shpm = B.t_shpf
            LEFT JOIN OneTime WITH (NOLOCK) ON  OneTime.AdCode = A.t_itoa
        WHERE A.t_tran IN ('S01', 'S02') 
          AND A.t_stat = 6
    ) 
    SELECT  *
    FROM InvoiceInfo
    WHERE ddd BETWEEN ? AND ?
    ";
    return DB::connection('sqlsrv')->select($query, [$fromDate, $toDate]);
}

}
