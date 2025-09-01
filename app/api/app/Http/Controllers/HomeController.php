<?php

namespace App\Http\Controllers;

use App\Constants\UserPermissions;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('app');
    }

    /**
     * Get initial data for Vue.js application
     */
    public function vue()
    {
        $mainMenu = [
            [
                'label' => 'admin.myprofile',
                'name' => 'myprofile',
                'route' => 'myprofile',
                'permission' => UserPermissions::WRITE_PROFILE,
            ],
            [
                'label' => 'admin.dashboard',
                'name' => 'item_dashboard',
                'route' => 'dashboard',
                'permission' => UserPermissions::DASHBOARD_VIEW,
            ],
            [
                'label' => 'admin.users.main',
                'name' => 'item_users',
                'route' => 'users',
                'permission' => UserPermissions::READ_USERS,
            ],
            [
                'label' => 'admin.leave_types.main',
                'name' => 'item_types',
                'route' => 'leave_types',
                'permission' => UserPermissions::DELETE_USERS,
            ],
            [
                'label' => 'admin.countries.main',
                'name' => 'countries',
                'route' => 'countries',
                'permission' => UserPermissions::DELETE_USERS,
            ],
            [
                'label' => 'admin.national_holidays.main',
                'name' => 'national_holidays',
                'route' => 'national_holidays',
                'permission' => UserPermissions::DELETE_USERS,
            ],
            // QUICK ACCESS NEW LEAVE REQUEST
            [
                'label' => 'admin.leave_requests.new',
                'name' => 'item_requests_mine',
                'route' => 'add.leave_request',
                'permission' => UserPermissions::READ_REQUESTS,
            ],
            [
                'label' => 'admin.leave_requests.mine',
                'name' => 'item_requests',
                'route' => 'leave_requests',
                'permission' => UserPermissions::READ_REQUESTS,
            ],
            [
                'label' => 'admin.leave_requests.all',
                'name' => 'item_requests_all',
                'route' => 'leave_requests_all',
                'permission' => UserPermissions::ALL_REQUESTS,
            ],
            // [
            //     'label' => 'Vacation Days',
            //     'name' => 'item_types',
            //     'route' => 'vacation_days',
            //     'permission' => UserPermissions::READ_REQUESTS,
            // ],
        ];

        $pagesItems = [
            'listStyle' => 'icons',
            'stickToSide' => "left",
            'items' => [
                [
                    'label' => 'My account',
                    'route' => 'demo1/index.html',
                    'icon' => 'IconArchive'
                ],
                [
                    'label' => 'Task manager',
                    'route' => '#',
                    'icon' => 'IconArchive',
                    'badge' => [
                        'theme' => 'success',
                        'label' => '2'
                    ]
                ],
                [
                    'label' => 'Team manager',
                    'route' => '#',
                    'icon' => 'IconArchive'
                ],
                [
                    'label' => 'Projects manager',
                    'route' => '#',
                    'icon' => 'IconArchive',
                    'submenu' => [
                        'stickToSide' => 'right',
                        'listStyle' => 'dot',
                        'items' => [
                            [
                                'label' => 'Add team members',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Edit team members',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Delete team members',
                                'route' => '#',
                            ]
                        ]
                    ]
                ],
                [
                    'label' => 'Create New Project',
                    'route' => 'demo2/index.html',
                    'icon' => 'IconArchive'
                ]
            ]
        ];

        $appsItems = [
            'listStyle' => 'icons',
            'stickToSide' => "left",
            'items' => [
                [
                    'label' => 'Reporting',
                    'route' => 'demo2/index.html',
                    'icon' => 'IconArchive'
                ],
                [
                    'label' => 'Social Presence',
                    'route' => '#',
                    'icon' => 'IconArchive'
                ],
                [
                    'label' => 'Deployment Center',
                    'route' => 'demo2/index.html',
                    'icon' => 'IconArchive',
                    'submenu' => [
                        'stickToSide' => 'right',
                        'listStyle' => 'line',
                        'items' => [
                            [
                                'label' => 'Merge Branch',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Version Controls',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Database Manager',
                                'route' => '#',
                            ]
                        ]
                    ]
                ],
                [
                    'label' => 'Campaigns',
                    'route' => '#',
                    'icon' => 'IconArchive',
                    'badge' => [
                        'theme' => 'success',
                        'label' => '3'
                    ]
                ],
                [
                    'label' => 'Create New Project',
                    'route' => 'demo2/index.html',
                    'icon' => 'IconArchive'
                ]
            ]
        ];

        $featuresItems = [
            'listStyle' => 'dot',
            'stickToSide' => 'left',
            'isExpanded' => true,
            'items' => [
                [
                    'label' => 'Task reports',
                    'route' => '#',
                    'submenu' => [
                        'listStyle' => 'icons',
                        'stickToSide' => 'right',
                        'items' => [
                            [
                                'label' => 'Latest task',
                                'icon' => 'IconArchive',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Pending tasks',
                                'icon' => 'IconATM',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Urgent tasks',
                                'icon' => 'IconBatteryhalf',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Failed tasks',
                                'icon' => 'IconATM',
                                'route' => '#',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Profit margins',
                    'route' => '#',
                    'submenu' => [
                        'listStyle' => 'line',
                        'stickToSide' => 'right',
                        'items' => [
                            [
                                'label' => 'Overall Profits',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Gross Profits',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Nett Profits',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Year to Date Reports',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Quarterly Profits',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Monthly Profits',
                                'route' => '#',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Staff management',
                    'route' => '#',
                    'submenu' => [
                        'listStyle' => 'dot',
                        'stickToSide' => 'right',
                        'items' => [
                            [
                                'label' => 'Top Management',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Project Managers',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Development Staff',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Customer Service',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Sales and Marketing',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Executives',
                                'route' => '#',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Tools',
                    'route' => '#',
                    'submenu' => [
                        'stickToSide' => 'right',
                        'items' => [
                            [
                                'label' => 'Analytical Reports',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Customer CRM',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Operational Growth',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Social Media Presence',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Files and Directories',
                                'route' => '#',
                            ],
                            [
                                'label' => 'Audit & Logs',
                                'route' => '#',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $navMenu = [
            [
                'label' => 'Pages',
                'route' => '#',
                'submenu' => $pagesItems
            ],
            [
                'label' => 'Apps',
                'route' => '#',
                'submenu' => $appsItems
            ],
            [
                'label' => 'Features',
                'route' => '#',
                'submenu' => $featuresItems
            ]
        ];

        return [
            'mainMenu' => $mainMenu,
            'navMenu' => [],
        ];
    }
}
