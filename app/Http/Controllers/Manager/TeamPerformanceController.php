<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportService;

class TeamPerformanceController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        $leaderboard = $this->reportService->getTeamLeaderboard();
        return view('manager.team.index', compact('leaderboard'));
    }

    public function show(User $user)
    {
        $memberData = $this->reportService->getTeamMemberDetail($user->id);
        return view('manager.team.show', compact('memberData'));
    }
}