<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\DataTransferObject\ProductDTO;
use App\Form\ProductType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class ProductAdmin extends AbstractAdmin
{

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('addedAt')
            ->add('discontinuedAt')
            ->add('timestamp')
            ->add('stock')
            ->add('cost')
            ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('addedAt')
            ->add('discontinuedAt')
            ->add('timestamp')
            ->add('stock')
            ->add('cost')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('addedAt')
            ->add('discontinuedAt')
            ->add('timestamp')
            ->add('stock')
            ->add('cost')
            ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('addedAt')
            ->add('discontinuedAt')
            ->add('timestamp')
            ->add('stock')
            ->add('cost')
            ;
    }
}
