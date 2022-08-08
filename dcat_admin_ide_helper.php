<?php

/**
 * A helper file for Dcat Admin, to provide autocomplete information to your IDE
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author jqh <841324345@qq.com>
 */
namespace Dcat\Admin {
    use Illuminate\Support\Collection;

    /**
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection scene
     * @property Grid\Column|Collection flow_type
     * @property Grid\Column|Collection money
     * @property Grid\Column|Collection before_balance
     * @property Grid\Column|Collection after_balance
     * @property Grid\Column|Collection describe
     * @property Grid\Column|Collection deleted_at
     * @property Grid\Column|Collection pay_type
     * @property Grid\Column|Collection apply_status
     * @property Grid\Column|Collection audit_date
     * @property Grid\Column|Collection reject_reason
     * @property Grid\Column|Collection is_settled
     * @property Grid\Column|Collection settle_date
     * @property Grid\Column|Collection payment_no
     * @property Grid\Column|Collection total_money
     * @property Grid\Column|Collection service_fee
     * @property Grid\Column|Collection nickname
     * @property Grid\Column|Collection head_img
     * @property Grid\Column|Collection open_id
     * @property Grid\Column|Collection session_key
     * @property Grid\Column|Collection balance
     * @property Grid\Column|Collection wxphone
     * @property Grid\Column|Collection gender
     * @property Grid\Column|Collection pay_money
     * @property Grid\Column|Collection is_merchant
     * @property Grid\Column|Collection ip
     * @property Grid\Column|Collection uuid
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection migration
     * @property Grid\Column|Collection batch
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection tokenable_type
     * @property Grid\Column|Collection tokenable_id
     * @property Grid\Column|Collection abilities
     * @property Grid\Column|Collection last_used_at
     * @property Grid\Column|Collection key
     * @property Grid\Column|Collection values
     * @property Grid\Column|Collection email_verified_at
     *
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection scene(string $label = null)
     * @method Grid\Column|Collection flow_type(string $label = null)
     * @method Grid\Column|Collection money(string $label = null)
     * @method Grid\Column|Collection before_balance(string $label = null)
     * @method Grid\Column|Collection after_balance(string $label = null)
     * @method Grid\Column|Collection describe(string $label = null)
     * @method Grid\Column|Collection deleted_at(string $label = null)
     * @method Grid\Column|Collection pay_type(string $label = null)
     * @method Grid\Column|Collection apply_status(string $label = null)
     * @method Grid\Column|Collection audit_date(string $label = null)
     * @method Grid\Column|Collection reject_reason(string $label = null)
     * @method Grid\Column|Collection is_settled(string $label = null)
     * @method Grid\Column|Collection settle_date(string $label = null)
     * @method Grid\Column|Collection payment_no(string $label = null)
     * @method Grid\Column|Collection total_money(string $label = null)
     * @method Grid\Column|Collection service_fee(string $label = null)
     * @method Grid\Column|Collection nickname(string $label = null)
     * @method Grid\Column|Collection head_img(string $label = null)
     * @method Grid\Column|Collection open_id(string $label = null)
     * @method Grid\Column|Collection session_key(string $label = null)
     * @method Grid\Column|Collection balance(string $label = null)
     * @method Grid\Column|Collection wxphone(string $label = null)
     * @method Grid\Column|Collection gender(string $label = null)
     * @method Grid\Column|Collection pay_money(string $label = null)
     * @method Grid\Column|Collection is_merchant(string $label = null)
     * @method Grid\Column|Collection ip(string $label = null)
     * @method Grid\Column|Collection uuid(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection migration(string $label = null)
     * @method Grid\Column|Collection batch(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection tokenable_type(string $label = null)
     * @method Grid\Column|Collection tokenable_id(string $label = null)
     * @method Grid\Column|Collection abilities(string $label = null)
     * @method Grid\Column|Collection last_used_at(string $label = null)
     * @method Grid\Column|Collection key(string $label = null)
     * @method Grid\Column|Collection values(string $label = null)
     * @method Grid\Column|Collection email_verified_at(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection id
     * @property Show\Field|Collection name
     * @property Show\Field|Collection type
     * @property Show\Field|Collection version
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection order
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection username
     * @property Show\Field|Collection password
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection scene
     * @property Show\Field|Collection flow_type
     * @property Show\Field|Collection money
     * @property Show\Field|Collection before_balance
     * @property Show\Field|Collection after_balance
     * @property Show\Field|Collection describe
     * @property Show\Field|Collection deleted_at
     * @property Show\Field|Collection pay_type
     * @property Show\Field|Collection apply_status
     * @property Show\Field|Collection audit_date
     * @property Show\Field|Collection reject_reason
     * @property Show\Field|Collection is_settled
     * @property Show\Field|Collection settle_date
     * @property Show\Field|Collection payment_no
     * @property Show\Field|Collection total_money
     * @property Show\Field|Collection service_fee
     * @property Show\Field|Collection nickname
     * @property Show\Field|Collection head_img
     * @property Show\Field|Collection open_id
     * @property Show\Field|Collection session_key
     * @property Show\Field|Collection balance
     * @property Show\Field|Collection wxphone
     * @property Show\Field|Collection gender
     * @property Show\Field|Collection pay_money
     * @property Show\Field|Collection is_merchant
     * @property Show\Field|Collection ip
     * @property Show\Field|Collection uuid
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection migration
     * @property Show\Field|Collection batch
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection tokenable_type
     * @property Show\Field|Collection tokenable_id
     * @property Show\Field|Collection abilities
     * @property Show\Field|Collection last_used_at
     * @property Show\Field|Collection key
     * @property Show\Field|Collection values
     * @property Show\Field|Collection email_verified_at
     *
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection scene(string $label = null)
     * @method Show\Field|Collection flow_type(string $label = null)
     * @method Show\Field|Collection money(string $label = null)
     * @method Show\Field|Collection before_balance(string $label = null)
     * @method Show\Field|Collection after_balance(string $label = null)
     * @method Show\Field|Collection describe(string $label = null)
     * @method Show\Field|Collection deleted_at(string $label = null)
     * @method Show\Field|Collection pay_type(string $label = null)
     * @method Show\Field|Collection apply_status(string $label = null)
     * @method Show\Field|Collection audit_date(string $label = null)
     * @method Show\Field|Collection reject_reason(string $label = null)
     * @method Show\Field|Collection is_settled(string $label = null)
     * @method Show\Field|Collection settle_date(string $label = null)
     * @method Show\Field|Collection payment_no(string $label = null)
     * @method Show\Field|Collection total_money(string $label = null)
     * @method Show\Field|Collection service_fee(string $label = null)
     * @method Show\Field|Collection nickname(string $label = null)
     * @method Show\Field|Collection head_img(string $label = null)
     * @method Show\Field|Collection open_id(string $label = null)
     * @method Show\Field|Collection session_key(string $label = null)
     * @method Show\Field|Collection balance(string $label = null)
     * @method Show\Field|Collection wxphone(string $label = null)
     * @method Show\Field|Collection gender(string $label = null)
     * @method Show\Field|Collection pay_money(string $label = null)
     * @method Show\Field|Collection is_merchant(string $label = null)
     * @method Show\Field|Collection ip(string $label = null)
     * @method Show\Field|Collection uuid(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection migration(string $label = null)
     * @method Show\Field|Collection batch(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection tokenable_type(string $label = null)
     * @method Show\Field|Collection tokenable_id(string $label = null)
     * @method Show\Field|Collection abilities(string $label = null)
     * @method Show\Field|Collection last_used_at(string $label = null)
     * @method Show\Field|Collection key(string $label = null)
     * @method Show\Field|Collection values(string $label = null)
     * @method Show\Field|Collection email_verified_at(string $label = null)
     */
    class Show {}

    /**
     
     */
    class Form {}

}

namespace Dcat\Admin\Grid {
    /**
     
     */
    class Column {}

    /**
     
     */
    class Filter {}
}

namespace Dcat\Admin\Show {
    /**
     
     */
    class Field {}
}
