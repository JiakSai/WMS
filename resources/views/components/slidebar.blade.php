@props(['org'])

<div id="sidebar" class="app-sidebar">

    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
    
        <div class="menu">
            <div class="menu-header">SMTT</div>
            @foreach( $mainModules as $mainModule )
                <div class="menu-item has-sub">
                    <a href="#" class="menu-link">
                        <span class="menu-icon"><i class="{{$mainModule->icon}}"></i></span>
                        <span class="menu-text">{{$mainModule->name}}</span>
                        <span class="menu-caret"><b class="caret"></b></span>
                    </a>
                    <div class="menu-submenu">
                        @foreach( $subModules as $subModule )
                        @if( $subModule->group == $mainModule->id)
                            <div class="menu-item">
                                <a href="{{ route('select-module', ['organisation' => $org->id, 'subModule'=> $subModule->id]) }}" class="menu-link">
                                    <span class="menu-text">{{$subModule->name}}</span>
                                </a>
                            </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
</div>