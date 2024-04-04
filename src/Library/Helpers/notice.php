<?php
if (!function_exists('gc_notice_add')) {
    /**
     * [gc_notice_add description]
     *
     * @param   string  $type    [$type description]
     * @param   string  $typeId  [$typeId description]
     *
     * @return  [type]           [return description]
     */
    function gc_notice_add(string $type, string $typeId)
    {
        $modelNotice = new Glumbo\Gracart\Admin\Models\AdminNotice;
        $content = '';
        $admins = [];
        switch ($type) {
            case 'gc_customer_created':
                $admins = gc_admin_notice_get_admin($type);
                $content = "admin_notice.customer.new";
                break;
            case 'gc_order_created':
                $admins = gc_admin_notice_get_admin($type);
                $content = "admin_notice.order.new";
                break;
            case 'gc_order_success':
                $admins = gc_admin_notice_get_admin($type);
                $content = "admin_notice.order.success";
                break;
            case 'gc_order_update_status':
                $admins = gc_admin_notice_get_admin($type);
                $content = "admin_notice.order.update_status";
                break;
            
            default:
                $admins = gc_admin_notice_get_admin($type);
                $content = $type;
                break;
        }
        if (count($admins)) {
            foreach ($admins as $key => $admin) {
                $modelNotice->create(
                    [
                        'type' => $type,
                        'type_id' => $typeId,
                        'admin_id' => $admin,
                        'content' => $content
                    ]
                );
            }
        }

    }

    /**
     * Get list id admin can get notice
     */
    if (!function_exists('gc_admin_notice_get_admin')) {
        function gc_admin_notice_get_admin(string $type = "")
        {
            if (function_exists('gc_admin_notice_pro_get_admin')) {
                return gc_admin_notice_pro_get_admin($type);
            }

            return (new \Glumbo\Gracart\Admin\Models\AdminUser)
            ->selectRaw('distinct '. GC_DB_PREFIX.'admin_user.id')
            ->join(GC_DB_PREFIX . 'admin_role_user', GC_DB_PREFIX . 'admin_role_user.user_id', GC_DB_PREFIX . 'admin_user.id')
            ->join(GC_DB_PREFIX . 'admin_role', GC_DB_PREFIX . 'admin_role.id', GC_DB_PREFIX . 'admin_role_user.role_id')
            ->whereIn(GC_DB_PREFIX . 'admin_role.slug', ['administrator','view.all', 'manager'])
            ->pluck('id')
            ->toArray();
        }
    }

}
