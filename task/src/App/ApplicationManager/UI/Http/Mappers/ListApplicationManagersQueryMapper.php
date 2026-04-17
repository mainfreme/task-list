<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Mappers;

use App\ApplicationManager\Application\Query\ListApplicationManagers\ListApplicationManagersQuery;
use Illuminate\Http\Request;

final class ListApplicationManagersQueryMapper
{
    public static function fromRequest(Request $request): ListApplicationManagersQuery
    {
        $isActive = null;
        if ($request->has('is_active')) {
            $isActive = $request->boolean('is_active');
        }

        return new ListApplicationManagersQuery(isActive: $isActive);
    }
}
