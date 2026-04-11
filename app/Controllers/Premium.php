<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SubscriptionModel;

class Premium extends BaseController
{
    public function plans()
    {
        $subscriptionModel = model(SubscriptionModel::class);
        $plans = $subscriptionModel->getActivePlans();
        $userId = (int) session()->get('user_id');
        $selectedService = strtolower(trim((string) ($this->request->getGet('service') ?? 'all')));

        return view('premium/plans', [
            'title' => 'Premium Services Plans - HireMatrix',
            'plans' => $plans,
            'current_subscription' => $userId > 0 ? $subscriptionModel->getUserActiveSubscription($userId) : null,
            'selected_service' => $selectedService,
        ]);
    }
}
