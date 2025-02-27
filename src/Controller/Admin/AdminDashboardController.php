<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Cancion;
use App\Entity\Usuario;
use App\Entity\Playlist;
use App\Entity\Estilo;
use App\Entity\Perfil;
use App\Entity\PlaylistCancion;
use App\Entity\UsuarioPlaylist;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/dashboard.html.twig', [
            'title' => 'Panel de Administración',
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Gestión de Spotify')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Usuarios', 'fa fa-users', Usuario::class);
        //yield MenuItem::linkToCrud('Perfiles', 'fa fa-id-card', Perfil::class);
        yield MenuItem::linkToCrud('Canciones', 'fa fa-music', Cancion::class);
        yield MenuItem::linkToCrud('Playlists', 'fa fa-list', Playlist::class);
        yield MenuItem::linkToCrud('Estilos', 'fa fa-palette', Estilo::class);
        //yield MenuItem::linkToCrud('Añadir Canciones a una Playlist', 'fa fa-plus', PlaylistCancion::class);
        yield MenuItem::linkToCrud('Historial de Playlists Escuchadas', 'fa fa-history', UsuarioPlaylist::class);
        
    }
}
