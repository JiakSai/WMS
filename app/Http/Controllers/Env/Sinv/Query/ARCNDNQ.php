<?php

namespace App\Http\Controllers\Env\Sinv\Query;
use Illuminate\Support\Facades\DB;

class ARCNDNQ
{
   public static function getCNDN($fromDate, $toDate)
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
  t_ln04 + t_ln05 AS Add3 ,t_cste as StateCode
,A.t_nama as OTname
FROM 
  erp.dbo.ttccom130800 A WITH (NOLOCK) 
  LEFT JOIN erp.dbo.ttccom100800 D WITH (NOLOCK) ON D.t_cadr = A.t_cadr 
left join RankedRecords b on D.t_bpid = b.t_bpid and rank =1
		where D.t_prst = 2
), OneTime AS(
		select 
		t_cadr as AdCode,t_ln01 as OTname, t_dsca  as City,t_ccty as Country,t_telp as Tel,
		       t_ln02  AS Add1
		,t_ln03  as Add2, 
		t_ln04+t_ln05  AS Add3,t_cste as StateCode
		from  erp.dbo.ttccom130800  WITH (NOLOCK) 
		
),
InvoiceInfo AS (
  SELECT 
    'ARCNDN' AS DocumentType, 
    '800' + '/' + CONVERT(VARCHAR(50), A.t_tran) + '/' + CONVERT(VARCHAR(50), A.t_idoc) AS InvoiceID,
    --'test-01' as InvoiceID,
      case when  A.t_tran IN ('SCN','S10') then 'Credit Note' else 'Debit Note' end as Type, 
    FORMAT(DATEADD(HOUR, 8, A.t_idat), 'MM/dd/yyyy HH:mm:ss') AS DocumentDate, 
    A.t_itbp AS CusCode, 
    	 	  case when A.t_itbp= 'OT0000001' then 'EI00000000010'
	   when CUS.Country != 'MYS' then 'EI00000000010'
	  else  CUS.TIN end AS TIN,
	    case when A.t_itbp= 'OT0000001' then 'NA' else  CUS.BRN end AS BRN,
       case when A.t_itbp= 'OT0000001' then OneTime.OTname else  CUS.RegName end AS CusRegName,
    case when A.t_itbp= 'OT0000001' then OneTime.Add1 else   CUS.Add1 end AS CusAddress1,
    case when A.t_itbp= 'OT0000001' then OneTime.Add2 else  CUS.Add2 end AS CusAddress2,
     case when A.t_itbp= 'OT0000001' then OneTime.Add3 else CUS.Add3 end AS CusAddress3,
    case when A.t_itbp= 'OT0000001' then OneTime.Country else CUS.Country end AS Country, 
		 case when A.t_itbp= 'OT0000001' then OneTime.City else CUS.City end AS City, 
     case when A.t_itbp= 'OT0000001' then OneTime.StateCode  else CUS.StateCode end AS StateCode,
	 	 case when A.t_itbp= 'OT0000001' then OneTime.Tel else CUS.Tel end AS Tel, 
    A.t_ccur AS Currency, 
    A.t_rate_1 AS CurrencyRate, 
    F.t_orno  AS SoRef, 
    C.t_corn as CusPo,
    B.t_shpf AS DoRef, 
     case when A.t_itbp = 'MA0000007' then '60 Days' else  I.t_dsca end AS Terms,
    A.t_amti AS InvoiceTotalAmount,  
	 '' AS PartNo, 
		case when ltrim(D.t_item) in ('Z05','Z07') then '027' else '022' end as Classification,

	case when  A.t_tran in( 'SCN','SDN') then H.t_refa else G.t_dsca end as ItemDescription,
    D.t_dqua AS [InvoiceQty], 
    Case when F.t_cups = '/p2' then D.t_pric/100 else D.t_pric end AS UnitPrice, 
    06 AS TaxType, 
    0 AS TaxRate, 
    0 AS TaxAmount, 
    0 AS TaxPrice, 
    D.t_amti AS ItemAmt, 
    	D.t_amth_1 as AmtInMyr,
	case when B.t_slsf ='' then J.t_cuni else D.t_cuqs end as OrderUOM,
	--case when A.t_tran = 'S10' ,
    
	   case when A.t_itbp= 'OT0000001' then OneTime.OTname else   Ship.RegName  end AS ShipReceiptName,
      case when A.t_itbp= 'OT0000001' then OneTime.Add1 else Ship.Add1 end AS ShipAddress1, 
     case when A.t_itbp= 'OT0000001' then OneTime.Add2 else Ship.Add2 end AS ShipAddress2, 
    case when A.t_itbp= 'OT0000001' then OneTime.Add3 else  Ship.Add3 end AS ShipAddress3, 
     case when A.t_itbp= 'OT0000001' then OneTime.Country else  Ship.Country end AS ShipCountry
      ,(DATEADD(HOUR, 8, A.t_idat)) as ddd
    ,case when A.t_tran = 'S10' then F.t_corp else H.t_msid end as EInvRefNo,B.t_slsf 
  FROM 
    erp.dbo.tcisli305800 A WITH (NOLOCK) 
    LEFT JOIN erp.dbo.tcisli200800 B WITH (NOLOCK) ON B.t_brid = A.t_brid 
    LEFT JOIN erp.dbo.ttdsls400800 C WITH (NOLOCK) ON C.t_orno = B.t_slsf 
    LEFT JOIN erp.dbo.tcisli310800 D WITH (NOLOCK) ON D.t_idoc = A.t_idoc AND D.t_tran = A.t_tran 
    LEFT JOIN erp.dbo.ttdsls401800 F WITH (NOLOCK) ON F.t_orno = C.t_orno AND F.t_invn = A.t_idoc AND F.t_pono = D.t_pono 
    LEFT JOIN erp.dbo.ttcibd001800 G WITH (NOLOCK) ON G.t_item = F.t_item 
    LEFT JOIN Address Ship ON Ship.BP = D.t_stbp
    LEFT JOIN Address CUS ON CUS.BP = D.t_ofbp
	
    LEFT join erp.dbo.tcisli220800 H WITH (NOLOCK) on A.t_msid = H.t_msid
	LEFT join erp.dbo.tcisli225800 J WITH (NOLOCK) on A.t_msid = J.t_msid and J.t_msln = D.t_pono 
	left join OneTime on  OneTime.AdCode = A.t_itoa
	left  join erp.dbo.ttcmcs013800 I WITH (NOLOCK) on I.t_cpay = A.t_cpay 
    WHERE A.t_tran IN ('SCN', 'SDN','S10') 
	--and DATEPART(YEAR,(DATEADD(HOUR, 8, A.t_idat))) = 2024 AND DATEPART(MONTH,(DATEADD(HOUR, 8, A.t_idat))) = 8
and A.t_idoc not in (25002103)

		) 
SELECT 
* 
FROM 
  InvoiceInfo
    WHERE ddd BETWEEN ? AND ?
    ";
    return DB::connection('sqlsrv')->select($query, [$fromDate, $toDate]);
}

}
