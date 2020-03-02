<?php

declare(strict_types=1);

namespace App\Admin;

use App\Service\ImportTool\FileDataValidator;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var
     */
    private $em;

    /**
     * ProductAdmin constructor.
     * @param EntityManagerInterface $em
     * @param $code
     * @param $class
     * @param $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($code, $class, $baseControllerName);
    }

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
            ->add('discontinuedAt')
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
    public function validate(ErrorElement $errorElement, $object): void
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
                ->addConstraint(new LessThan(FileDataValidator::PRODUCT_RULE_MAX_COST))
                ->assertNotBlank()
            ->end()
            ->with('stock')
                ->assertNotBlank()
            ->end()
        ;

        if ($errorElement->getSubject()->getCost() < FileDataValidator::PRODUCT_RULE_MIN_COST &&
            $errorElement->getSubject()->getStock() < FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
        ) {
            $errorElement->with('stock')
                ->addViolation(
                    'Stock is less than '.FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
                    .' and cost is less than '. FileDataValidator::PRODUCT_RULE_MIN_COST)
                ->end()
            ;
        }

        $entityObject = $errorElement->getSubject();
        $entityName = get_class($entityObject);

        $foundedObject = $this->em->getRepository($entityName)
            ->findOneByCode($errorElement->getSubject()->getCode());

        if ($foundedObject) {

            if ($errorElement->getSubject()->getId() != $foundedObject->getId()) {
                $errorElement->with('code')
                    ->addViolation('Product with this code is already existing!')
                    ->end()
                ;
            }
        }

        if ($entityObject->getStock() === 0) {
            $entityObject->setDiscontinuedAt(new \DateTime());
        }
    }
}
