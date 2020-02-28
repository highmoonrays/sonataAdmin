<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

final class ProductAdminController extends CRUDController
{
    public function editAction($id = null)
    {
        return $this->redirectToRoute('editProduct', [
            'id' => $id,
        ]);
    }

    public function  createAction()
    {
        return $this->redirectToRoute('createNewProduct');
    }
}
