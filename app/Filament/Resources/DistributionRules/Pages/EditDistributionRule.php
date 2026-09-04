<?php
namespace App\Filament\Resources\DistributionRules\Pages;
use App\Filament\Resources\DistributionRules\DistributionRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditDistributionRule extends EditRecord { protected static string $resource = DistributionRuleResource::class; protected function getHeaderActions(): array { return [DeleteAction::make()]; } }
