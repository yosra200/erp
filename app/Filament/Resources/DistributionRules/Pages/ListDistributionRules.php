<?php
namespace App\Filament\Resources\DistributionRules\Pages;
use App\Filament\Resources\DistributionRules\DistributionRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListDistributionRules extends ListRecords { protected static string $resource = DistributionRuleResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
