<?php

namespace Hanafalah\ModuleWorkspace\Resources\Workspace;

use Illuminate\Http\Request;
use Hanafalah\ModuleRegional\Resources\Address\ShowAddress;

class ShowWorkspace extends ViewWorkspace
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $arr = [
            'setting' => $this->toSettingApi(),
            'integration' => $this->normalizeIntegration($this->integration)
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }

    /**
     * Normalize integration data to ensure from/to are calculated.
     *
     * @param array|null $integration
     * @return array|null
     */
    protected function normalizeIntegration(?array $integration): ?array
    {
        if (empty($integration)) {
            return $integration;
        }

        // Normalize satu_sehat integration
        if (isset($integration['satu_sehat']) && isset($integration['satu_sehat']['syncs'])) {
            $integration['satu_sehat'] = $this->recalculateSatuSehatTotals($integration['satu_sehat']);
        }

        return $integration;
    }

    /**
     * Recalculate from/to totals for satu_sehat integration.
     *
     * @param array $satuSehat
     * @return array
     */
    protected function recalculateSatuSehatTotals(array $satuSehat): array
    {
        $totalFrom = 0;
        $totalTo = 0;

        foreach ($satuSehat['syncs'] ?? [] as $sync) {
            $totalFrom += (int)($sync['from'] ?? 0);
            $totalTo += (int)($sync['to'] ?? 0);
        }

        $satuSehat['from'] = $totalFrom;
        $satuSehat['to'] = $totalTo;
        $satuSehat['progress'] = $totalTo > 0 ? round(($totalFrom / $totalTo) * 100, 2) : 0;

        // Limit logs to 20 entries (newest first)
        if (isset($satuSehat['logs']) && count($satuSehat['logs']) > 20) {
            $satuSehat['logs'] = array_slice($satuSehat['logs'], 0, 20);
        }

        return $satuSehat;
    }
}
