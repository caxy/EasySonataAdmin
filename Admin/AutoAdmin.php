<?php

namespace Caxy\EasySonataAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\FormEvent;

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

        if ($this->config['batch_actions'] !== null) {
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
        $config = $this->config['edit'];

        if ($config) {
            foreach ($config['fields'] as $field) {
                $form->add($field['property'], array_key_exists('type', $field) ? $field['type'] : null, array_key_exists('type_options', $field) ? $field['type_options'] : null);
            }
            $actions = array();

            foreach ($config['actions'] as $action) {
                $actions[$action] = array();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $config = $this->config['list'];

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

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $config = $this->config['filter'];

        $this->buildMapper($config, $filter);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $filter)
    {
        $config = $this->config['show'];

        $this->buildMapper($config, $filter);
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
