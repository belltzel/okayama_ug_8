<?php

namespace Customize;

use Eccube\Common\EccubeNav;

class CustomizeNav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'customize_agency' => [
                'name' => 'customize.admin.agency.agency_management',
                'icon' => 'fa-user-tie',
                'children' => [
                    'agency_master' => [
                        'name' => 'customize.admin.agency.agency_list',
                        'url' => 'customize_admin_agency',
                    ],
                    'agency_edit' => [
                        'name' => 'customize.admin.agency.agency_registration',
                        'url' => 'customize_admin_agency_new',
                    ],
                ],
            ],
        ];
    }
}
