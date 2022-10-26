<?php

namespace App\Observers;

use App\Models\AgentBalanceHistory;
use App\Models\AgentCharge;
use App\Services\AgentBalanceHistoryService;
use App\Services\AgentChargeHistoryBalanceService;
use App\Services\AgentService;

class AgentChargeObserver
{
    public function __construct(
        private AgentChargeHistoryBalanceService $agentChargeHistoryBalanceService,
        private AgentBalanceHistoryService $agentBalanceHistoryService,
        private AgentService $agentService,
    ) {
    }

    public function created(AgentCharge $agentCharge): void
    {
        $agent = $this->agentService->getById($agentCharge->agent_id);
        $agentBalanceHistoryData = [
            'agent_id' => $agent->id,
            'amount' => request()->input('amount'),
            'available_balance' => $agent->balance,
            'due_to_supplier' => $agent->due_to_energy_supplier
        ];
        $agentBalanceHistory =  $this->agentBalanceHistoryService->make($agentBalanceHistoryData);
        $this->agentChargeHistoryBalanceService->setAssigned($agentBalanceHistory);
        $this->agentChargeHistoryBalanceService->setAssigner($agentCharge);
        $this->agentChargeHistoryBalanceService->assign();
        $this->agentBalanceHistoryService->save($agentBalanceHistory);
    }
}
