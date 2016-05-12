easy-sonata_admin
=================

###Getting Started

1) Add EasySonataAdminBundle to your project

```bash
composer require caxy/easy-sonata-admin
```

2) Enable the bundle

```bash
// app/AppKernal.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(

            //Load before SonataAdminBundle!
            new Caxy\EasySonataAdminBundle\EasySonataAdminBundle(),

            new Sonata\AdminBundle\SonataAdminBundle(),
        );
    }
}
```

3) Configure your admin.
Example for FOS User Entity below:

```bash
// app/config/config.yml

easy_sonata_admin:
    entities:
        User:
            class: AppBundle\Entity\User
            batch_actions:
                - '-delete'
            list:
                fields:
                    - id
                    - username
                    - email
                    - enabled
                actions: [show: {}, 'edit', 'delete']
            edit:
                fields:
                    - { property: id, type_options: { } }
                    - { property: username, type_options: { } }
                    - { property: email, type_options: { } }
                    - { property: enabled, type_options: { } }
                    - { property: 'plain_password', type: 'text', type_options: { required: true } }
                actions: ['-delete']
            filter:
                fields:
                    - username
                    - email
                    - enabled
                    - roles
            show:
                fields:
                    - username
                    - email
                    - enabled
                    - { property: 'last_login', type: 'datetime' }
                    - roles
                actions: ['-delete']

```