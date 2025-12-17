<?php

namespace App\Http\Controllers\Traits;

trait HasBulkActions
{
    public function bulkAction($request, $model, $actions)
    {
        $request->validate([
            'action' => 'required|in:' . implode(',', array_keys($actions)),
            'ids' => 'required|array',
            'ids.*' => 'exists:' . (new $model)->getTable() . ',id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        if (isset($actions[$action])) {
            $result = $actions[$action]($model, $ids);
            $message = $result['message'] ?? ucfirst($action) . ' action completed successfully.';
            
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }
}