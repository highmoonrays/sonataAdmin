<?php

declare(strict_types=1);

namespace App\Validator;

use App\Service\Tool\ObjectToAssociativeArrayTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class UniqueProductValidator
 * @package App\Validator\Constraint
 * @Annotation
 */
class CustomUniqueEntityValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ObjectToAssociativeArrayTransformer
     */
    private $objectToAssociativeArrayTransform;

    /**
     * UniqueProductValidator constructor.
     * @param EntityManagerInterface $em
     * @param ObjectToAssociativeArrayTransformer $objectToAssociativeArrayTransform
     */
    public function __construct(
        EntityManagerInterface $em,
        ObjectToAssociativeArrayTransformer $objectToAssociativeArrayTransform
    ) {
        $this->em = $em;
        $this->objectToAssociativeArrayTransform = $objectToAssociativeArrayTransform;
    }

    /**
     * @param mixed $objectToValidate
     * @param Constraint $constraint
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function validate($objectToValidate, Constraint $constraint): void
    {
        if (!$constraint instanceof CustomUniqueEntity) {
            throw new UnexpectedTypeException($constraint, CustomUniqueEntity::class);
        }

        if (null === $objectToValidate || '' === $objectToValidate) {
            return;
        }

        if (!is_object($objectToValidate)) {
            throw new UnexpectedValueException($objectToValidate, 'object');
        }
        $arrayToValidate = $this->objectToAssociativeArrayTransform->transform($objectToValidate);
        $criteria = [];

        foreach ($constraint->fields as $field) {
            $criteria[$field] = $arrayToValidate[$field];
        }
        $foundedObject = $this->em->getRepository($constraint->className)->findOneBy($criteria);

        if ($foundedObject) {
            $foundedAndTransformedObjectToCompare = $this->objectToAssociativeArrayTransform->transform($foundedObject);

            if (
                $foundedAndTransformedObjectToCompare[$constraint->primaryKey] ===
                $arrayToValidate[$constraint->primaryKey]
            ) {
                return;
            } else {
                $this->context->buildViolation($constraint->message)
                    ->atPath($constraint->fieldToFireError)
                    ->addViolation();
            }
        }
    }
}
