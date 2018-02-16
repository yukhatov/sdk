<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 17.07.17
 * Time: 19:17
 */

namespace AppBundle\Core;

interface DatedInterface
{
    public function getCreatedAt();
    public function setCreatedAt($createdAt);

    public function getUpdatedAt();
    public function setUpdatedAt($updatedAt);
}