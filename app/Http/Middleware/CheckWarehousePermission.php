<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserWarehousePermission;

class CheckWarehousePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
         $user = Auth::user();
        $warehouseId = $request->route('warehouse_id'); // from URL param

        if (!$user) {
            abort(403, 'You are not logged in.');
        }

        $record = UserWarehousePermission::where('user_id', $user->id)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if (!$record || !$record->{$permission}) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
