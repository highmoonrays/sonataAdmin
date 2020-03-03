<?php

declare(strict_types=1);

namespace App\Admin;

use App\Service\Admin\ProductAdminValidator;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ProductAdminValidator
     */
    private $productAdminValidator;

    /**
     * ProductAdmin constructor.
     * @param $code
     * @param $class
     * @param $baseControllerName
     * @param EntityManagerInterface $em
     * @param ProductAdminValidator $productAdminValidator
     */
    public function __construct(
        $code,
        $class,
        $baseControllerName,
        EntityManagerInterface $em,
        ProductAdminValidator $productAdminValidator
    ) {
        $this->em = $em;
        parent::__construct($code, $class, $baseControllerName);
        $this->productAdminValidator = $productAdminValidator;
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
        $this->productAdminValidator->validate($errorElement, $this->em);
    }
}
