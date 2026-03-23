<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    /**
     * Unauthenticated users should be redirected to login.
     */
    public function testUnauthenticatedUserRedirectsToLogin()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /**
     * Login page loads successfully.
     */
    public function testLoginPageLoads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Authenticated super-admin can access dashboard.
     */
    public function testSuperAdminCanAccessDashboard()
    {
        $user = User::role('super-admin')->first();

        if (!$user) {
            $this->markTestSkipped('No super-admin user found in database.');
        }

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    /**
     * Authenticated admin can access admin user index.
     */
    public function testAdminCanAccessUserIndex()
    {
        $user = User::role('super-admin')->first();

        if (!$user) {
            $this->markTestSkipped('No super-admin user found in database.');
        }

        $response = $this->actingAs($user)->get('/admin/users/index');
        $response->assertStatus(200);
    }

    /**
     * Investor cannot access admin routes.
     */
    public function testInvestorCannotAccessAdminRoutes()
    {
        $user = User::role('investor')->first();

        if (!$user) {
            $this->markTestSkipped('No investor user found in database.');
        }

        $response = $this->actingAs($user)->get('/admin/users/index');
        $response->assertStatus(403);
    }

    /**
     * Admin dashboard export returns a download response.
     */
    public function testAdminDashboardExportWorks()
    {
        $user = User::role('super-admin')->first();

        if (!$user) {
            $this->markTestSkipped('No super-admin user found in database.');
        }

        $response = $this->actingAs($user)->get('/api/admin/admin-dashboard-export?filterDate=all_time&sortDirection=ASC');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    /**
     * Route list command runs without errors (catches broken controller references).
     */
    public function testRouteListCommandRuns()
    {
        $this->artisan('route:list')->assertExitCode(0);
    }
}
