<?php


namespace App\Eventsubscriber;


use KevinPapst\AdminLTEBundle\Event\BreadcrumbMenuEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class MenuBuilderSubscriber implements EventSubscriberInterface
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SidebarMenuEvent::class => ['onSetupNavbar', 100],
            #BreadcrumbMenuEvent::class => ['onSetupNavbar', 100],
        ];
    }
   
    

    /**
     * Generate the main menu.
     *
     * @param SidebarMenuEvent $event
     */
    public function onSetupNavbar(SidebarMenuEvent $event)
    {
        $event->addItem(
            new MenuItemModel('dashboard', 'Accueil', 'home', [], 'fas fa-tachometer-alt')
        );
        $event->addItem(
            new MenuItemModel(
                'lignes',
                'Plans de lignes',
                'oziolab_bibus_bundle_walk',
                ['path' => $this->params->get('lignes_dir')],
                'fas fa-map-marker'
            )
        );
        $event->addItem(
            new MenuItemModel(
                'deviations',
                'Déviations',
                'oziolab_bibus_bundle_walk',
                ['path' => $this->params->get('deviation_dir')],
                'fas fa-level-up-alt'
            )
        );
        $event->addItem(
            new MenuItemModel(
                'horaires',
                'Navettes',
                'oziolab_bibus_bundle_walk',
                ['path' => $this->params->get('horaires_dir')],
                'fas fa-clock'
            )
        );
        $event->addItem(
            new MenuItemModel('echo', 'ECHO', 'oziolab_bibus_echo_redirect', [], 'fas fa-list')
        );
        $event->addItem(
            new MenuItemModel('informations', 'Bibus Connect', 'https://monkiosque.ratpdev.com/bibus/_login/', [], 'fas fa-info-circle')
        );
        $event->addItem(
            new MenuItemModel('aide', 'Bibus Com\'', 'https://docs.google.com/spreadsheets/d/1MXn2cCLe3r7XgbYKhA3D6ROZCRnjKxjAIVw0kmvHNY8/edit#gid=1423524341', [] , 'fas fa-calendar')
        );

        $this->activateByRoute(
            $event->getRequest()->get('_route'),
            $event->getItems()
        );
    }


    /**
     * @param string $route
     * @param MenuItemModel[] $items
     * 
     */
    protected function activateByRoute($route, $items)
    {
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            } elseif ($item->getRoute() == $route) {
                $item->setIsActive(false);
            }
        }
    }

   
}
