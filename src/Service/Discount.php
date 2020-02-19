<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use App\Entity\Product;
use App\Entity\Rules;

/**
 * Class Discount
 *
 * @package App\Service
 */
class Discount
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionlanguage;

    /**
     * @var Rules
     */
    private $rules;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * Discount constructor.
     *
     * @param ExpressionLanguage $expressionlanguage
     * @param Rules $rules
     * @param Product $product
     * @param ProductRepository $repository
     */
    public function __construct(
        ExpressionLanguage $expressionlanguage,
        Rules              $rules,
        Product            $product,
        ProductRepository  $repository
    )
    {
        $this->expressionlanguage = $expressionlanguage;
        $this->rules      = $rules;
        $this->product    = $product;
        $this->repository = $repository;
    }

    /**
     * Apply the reduction on the price
     */
    public function changeProductPrice()
    {
        foreach ($this->product as $item){
            $rules    = $this->rules->getRuleExpression();
            $price   = $this->product->getPrice();
            $new_price = ($this->rules->getDiscountPercent()*$price/100);

            foreach ($rules as $rule){
                $this->expressionlanguage->evaluate(
                    $rule,
                    $this->product);
            }

            $this->product->setDiscountedPrice($new_price);
            $this->repository->save($item);
        }
    }

}

