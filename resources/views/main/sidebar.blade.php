<nav class="flex flex-col gap-3 flex-1">
    <a href="#team" class="nav-link py-2 px-4 rounded hover:bg-green-600 transition flex items-center">
        <i class="fas fa-users mr-2"></i>Team List
    </a>
    
    <a href="#group" class="nav-link py-2 px-4 rounded hover:bg-green-600 transition flex items-center">
        <i class="fas fa-users-cog mr-2"></i>Case List
    </a>
    
    <a href="#user" class="nav-link py-2 px-4 rounded hover:bg-green-600 transition flex items-center">
        <i class="fas fa-user mr-2"></i>User List
    </a>
    
    {{-- UserTempListは外来Lawでは非表示 --}}
    
    <a href="#usageStatus" class="nav-link py-2 px-4 rounded hover:bg-green-600 transition flex items-center">
        <i class="fas fa-chart-bar mr-2"></i>利用状況
    </a>
</nav>

<div class="mt-auto text-right">
    <a href="{{ route('login') }}" class="text-sm underline hover:text-gray-200 flex items-center justify-end">
        LogOut <i class="fas fa-sign-out-alt ml-1"></i>
    </a>
</div>