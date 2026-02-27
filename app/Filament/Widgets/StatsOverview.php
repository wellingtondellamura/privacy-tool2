<?php

namespace App\Filament\Widgets;

use App\Models\Inspection;
use App\Models\Project;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Usuários', User::count())
                ->description('Usuários registrados no sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total de Projetos', Project::count())
                ->description('Projetos sob gestão')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            Stat::make('Inspeções', Inspection::count())
                ->description('Inspeções realizadas ou em andamento')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('warning'),
        ];
    }
}
