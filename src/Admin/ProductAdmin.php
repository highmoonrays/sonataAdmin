<?php

declare(strict_types=1);

namespace App\Admin;

use App\Service\ImportTool\FileDataValidator;
use App\Validator\CustomUniqueEntity;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints\LessThan;

/**
 * Class ProductAdmin
 * @package App\Admin
 */
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

    /**
     * @param ErrorElement $errorElement
     * @param object $object
     * @throws \Exception
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
                ->assertNotBlank()
            ->end()
            ->with('description')
                ->assertNotBlank()
            ->end()
            ->with('code')
                ->assertNotBlank()
            ->end()
            ->with('cost')
                ->addConstraint(new LessThan(1000))
                ->assertNotBlank()
            ->end()
            ->with('stock')
                ->assertNotBlank()
            ->end()
        ;

        if ($errorElement->getSubject()->getCost() < 5 && $errorElement->getSubject()->getStock() < 10) {
            $errorElement->with('stock')
                ->addViolation(
                    'Stock is less than '.FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
                    .' and cost is less than '. FileDataValidator::PRODUCT_RULE_MIN_COST)
                ->end();
        }

        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $foundedObject = $em->getRepository(get_class($errorElement->getSubject()))->findOneByCode($errorElement->getSubject()->getCode());

        if ($foundedObject) {
            if ($errorElement->getSubject()->getId() != $foundedObject->getId()) {
                $errorElement->with('code')
                    ->addViolation('Product with this code is already existing!')
                    ->end();
            }
        }
    }
}
