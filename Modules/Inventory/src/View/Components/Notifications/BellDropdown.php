<?php

namespace Modules\Inventory\View\Components\Notifications;

use Illuminate\View\Component;
use Illuminate\View\View;

class BellDropdown extends Component
{
    public function render(): View|string
    {
        return view('inventory::components.notifications.bell-dropdown');
    }
}
