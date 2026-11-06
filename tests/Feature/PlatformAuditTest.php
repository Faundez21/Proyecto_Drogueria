<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PlatformAuditTest extends TestCase
{
    protected ?User $admin = null;
    protected ?User $bodeguero = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'pabloadmin@drogueriadas.com')->first();
        $this->bodeguero = User::where('email', 'usuario@drogueriadas.com')->first();
    }

    public function test_admin_can_access_all_main_views(): void
    {
        $this->assertNotNull($this->admin, 'Usuario Administrador no existe');

        $routes = [
            '/',
            '/inventory',
            '/reception',
            '/dispatch',
            '/controlled-dispatch',
            '/requests',
            '/requests/cesfam',
            '/reports',
            '/quarantine',
            '/distribution',
            '/providers',
            '/users',
            '/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertTrue(
                in_array($response->getStatusCode(), [200, 302]),
                "Fallo al acceder a la ruta: {$route} con status {$response->getStatusCode()}"
            );
        }
    }

    public function test_admin_can_access_pending_requests_pdf_endpoints(): void
    {
        $this->assertNotNull($this->admin);

        // PDF Establecimientos
        $resEstab = $this->actingAs($this->admin)->get('/requests/pending-establecimientos-pdf?download=0');
        $this->assertEquals(200, $resEstab->getStatusCode(), 'Fallo en PDF Establecimientos');

        // PDF CESFAM
        $resCesfam = $this->actingAs($this->admin)->get('/requests/pending-cesfam-pdf?download=0');
        $this->assertEquals(200, $resCesfam->getStatusCode(), 'Fallo en PDF CESFAM');
    }

    public function test_bodeguero_role_permissions_and_restrictions(): void
    {
        $this->assertNotNull($this->bodeguero, 'Usuario Bodeguero no existe');

        // Bodeguero puede ver Inventario
        $resInv = $this->actingAs($this->bodeguero)->get('/inventory');
        $this->assertEquals(200, $resInv->getStatusCode(), 'Bodeguero debería poder ver /inventory');

        // Bodeguero NO puede acceder a gestión de usuarios (/users)
        $resUsers = $this->actingAs($this->bodeguero)->get('/users');
        $this->assertTrue(
            in_array($resUsers->getStatusCode(), [403, 302]),
            "Bodeguero no debería tener acceso a /users, obtuvo status: {$resUsers->getStatusCode()}"
        );
    }
}
