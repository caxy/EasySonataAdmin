<?php

namespace Caxy\EasySonataAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AutoAdmin extends Admin
{
    /**
     * @var array
     */
    private $config;

    /**
     * AutoAdmin constructor.
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param array  $config
     * @param string $name
     */
    public function __construct($code, $class, $baseControllerName, $config, $name)
    {
        $this->config = $config;

        if (array_key_exists('route_name', $config)) {
            $this->baseRouteName = $config['route_name'];
        } else {
            $this->baseRouteName = sprintf('admin_%s', strtolower($name));
        }

        if (array_key_exists('route_pattern', $config)) {
            $this->baseRoutePattern = $config['route_pattern'];
        } else {
            $this->baseRoutePattern = strtolower($name);
        }

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if (array_key_exists('batch_actions', $this->config) && $this->config['batch_actions'] !== null) {
            foreach ($this->config['batch_actions'] as $action) {
                if (substr($action, 0, 1) === '-') {
                    unset($actions[substr($action, 1)]);
                } else {
                    $actions[] = $action;
                }
            }
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        if($this->isNewObject() && $this->hasConfig('new')) {
            $method = 'new';
        } else {
            $method = 'form';
        }

        $this->preventUnauthorizedAccess($method);

        if (($config = $this->getConfig($method)) !== false) {
            $this->buildMapper($config, $form);
        }
    }

    /**
     * @return bool
     */
    protected function isNewObject()
    {
        return !$this->id($this->getSubject());
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $this->preventUnauthorizedAccess('list');

        if (($config = $this->getConfig('list')) !== false) {

            $this->buildMapper($config, $list);

            $actions = array();

            foreach ($config['actions'] as $action) {
                if (is_array($action)) {
                    $actions = array_merge($actions, $action);
                } else {
                    $actions[$action] = array();
                }
            }

            $list->add('_action', 'actions', array(
                'actions' => $actions,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        if(array_key_exists('filter', $this->config) && $this->config['filter']) {
            $config = $this->config['filter'];

            $this->buildMapper($config, $filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $filter)
    {
        $this->preventUnauthorizedAccess('show');

        if (($config = $this->getConfig('show')) !== false) {

            $this->buildMapper($config, $filter);
        }
    }

    private function preventUnauthorizedAccess($action)
    {
        $config = $this->getConfig($action);

        if($config && array_key_exists('role', $config)) {
            if(!$this->isGranted($config['role'])) {
                throw new AccessDeniedException();
            }
        }
    }

    private function hasConfig($action)
    {
        return array_key_exists($action, $this->config) && $this->config[$action];
    }

    private function getConfig($action)
    {
        if($this->hasConfig($action)) {
            return $this->config[$action];
        }
        return false;
    }

    /**
     * @param $config
     * @param ListMapper|ShowMapper|FormEvent|DatagridMapper $mapper
     */
    private function buildMapper($config, $mapper)
    {
        if ($config) {
            foreach ($config['fields'] as $field) {
                if (is_array($field)) {
                    $mapper->add($field['property'],
                        array_key_exists('type', $field) ? $field['type'] : null,
                        array_key_exists('type_options', $field) ? $field['type_options'] : array()
                    );
                } else {
                    $mapper->add($field);
                }
            }
        }
    }
}
