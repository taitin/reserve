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
     * @property Grid\Column|Collection content
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection deleted_at
     * @property Grid\Column|Collection do_method
     * @property Grid\Column|Collection from
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection keyword
     * @property Grid\Column|Collection params
     * @property Grid\Column|Collection target
     * @property Grid\Column|Collection text_buttons
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection discount_price
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection price
     * @property Grid\Column|Collection status
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection uuid
     * @property Grid\Column|Collection group_id
     * @property Grid\Column|Collection date
     * @property Grid\Column|Collection group_name
     * @property Grid\Column|Collection group_type
     * @property Grid\Column|Collection message_type
     * @property Grid\Column|Collection replied_at
     * @property Grid\Column|Collection reply_token
     * @property Grid\Column|Collection social_id
     * @property Grid\Column|Collection text
     * @property Grid\Column|Collection address
     * @property Grid\Column|Collection city
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection use_time
     * @property Grid\Column|Collection result
     * @property Grid\Column|Collection key
     * @property Grid\Column|Collection email_verified_at
     * @property Grid\Column|Collection addition_services
     * @property Grid\Column|Collection after_photos
     * @property Grid\Column|Collection arrive_at
     * @property Grid\Column|Collection before_photos
     * @property Grid\Column|Collection car_type
     * @property Grid\Column|Collection license
     * @property Grid\Column|Collection parking
     * @property Grid\Column|Collection pay_auth_result
     * @property Grid\Column|Collection pay_data
     * @property Grid\Column|Collection pay_result
     * @property Grid\Column|Collection phone
     * @property Grid\Column|Collection suggest_time
     * @property Grid\Column|Collection time
     * @property Grid\Column|Collection worker
     *
     * @method Grid\Column|Collection content(string $label = null)
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection deleted_at(string $label = null)
     * @method Grid\Column|Collection do_method(string $label = null)
     * @method Grid\Column|Collection from(string $label = null)
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection keyword(string $label = null)
     * @method Grid\Column|Collection params(string $label = null)
     * @method Grid\Column|Collection target(string $label = null)
     * @method Grid\Column|Collection text_buttons(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection discount_price(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection price(string $label = null)
     * @method Grid\Column|Collection status(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection uuid(string $label = null)
     * @method Grid\Column|Collection group_id(string $label = null)
     * @method Grid\Column|Collection date(string $label = null)
     * @method Grid\Column|Collection group_name(string $label = null)
     * @method Grid\Column|Collection group_type(string $label = null)
     * @method Grid\Column|Collection message_type(string $label = null)
     * @method Grid\Column|Collection replied_at(string $label = null)
     * @method Grid\Column|Collection reply_token(string $label = null)
     * @method Grid\Column|Collection social_id(string $label = null)
     * @method Grid\Column|Collection text(string $label = null)
     * @method Grid\Column|Collection address(string $label = null)
     * @method Grid\Column|Collection city(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection use_time(string $label = null)
     * @method Grid\Column|Collection result(string $label = null)
     * @method Grid\Column|Collection key(string $label = null)
     * @method Grid\Column|Collection email_verified_at(string $label = null)
     * @method Grid\Column|Collection addition_services(string $label = null)
     * @method Grid\Column|Collection after_photos(string $label = null)
     * @method Grid\Column|Collection arrive_at(string $label = null)
     * @method Grid\Column|Collection before_photos(string $label = null)
     * @method Grid\Column|Collection car_type(string $label = null)
     * @method Grid\Column|Collection license(string $label = null)
     * @method Grid\Column|Collection parking(string $label = null)
     * @method Grid\Column|Collection pay_auth_result(string $label = null)
     * @method Grid\Column|Collection pay_data(string $label = null)
     * @method Grid\Column|Collection pay_result(string $label = null)
     * @method Grid\Column|Collection phone(string $label = null)
     * @method Grid\Column|Collection suggest_time(string $label = null)
     * @method Grid\Column|Collection time(string $label = null)
     * @method Grid\Column|Collection worker(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection content
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection deleted_at
     * @property Show\Field|Collection do_method
     * @property Show\Field|Collection from
     * @property Show\Field|Collection id
     * @property Show\Field|Collection keyword
     * @property Show\Field|Collection params
     * @property Show\Field|Collection target
     * @property Show\Field|Collection text_buttons
     * @property Show\Field|Collection type
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection discount_price
     * @property Show\Field|Collection name
     * @property Show\Field|Collection price
     * @property Show\Field|Collection status
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection version
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection order
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection password
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection username
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection uuid
     * @property Show\Field|Collection group_id
     * @property Show\Field|Collection date
     * @property Show\Field|Collection group_name
     * @property Show\Field|Collection group_type
     * @property Show\Field|Collection message_type
     * @property Show\Field|Collection replied_at
     * @property Show\Field|Collection reply_token
     * @property Show\Field|Collection social_id
     * @property Show\Field|Collection text
     * @property Show\Field|Collection address
     * @property Show\Field|Collection city
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection use_time
     * @property Show\Field|Collection result
     * @property Show\Field|Collection key
     * @property Show\Field|Collection email_verified_at
     * @property Show\Field|Collection addition_services
     * @property Show\Field|Collection after_photos
     * @property Show\Field|Collection arrive_at
     * @property Show\Field|Collection before_photos
     * @property Show\Field|Collection car_type
     * @property Show\Field|Collection license
     * @property Show\Field|Collection parking
     * @property Show\Field|Collection pay_auth_result
     * @property Show\Field|Collection pay_data
     * @property Show\Field|Collection pay_result
     * @property Show\Field|Collection phone
     * @property Show\Field|Collection suggest_time
     * @property Show\Field|Collection time
     * @property Show\Field|Collection worker
     *
     * @method Show\Field|Collection content(string $label = null)
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection deleted_at(string $label = null)
     * @method Show\Field|Collection do_method(string $label = null)
     * @method Show\Field|Collection from(string $label = null)
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection keyword(string $label = null)
     * @method Show\Field|Collection params(string $label = null)
     * @method Show\Field|Collection target(string $label = null)
     * @method Show\Field|Collection text_buttons(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection discount_price(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection price(string $label = null)
     * @method Show\Field|Collection status(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection uuid(string $label = null)
     * @method Show\Field|Collection group_id(string $label = null)
     * @method Show\Field|Collection date(string $label = null)
     * @method Show\Field|Collection group_name(string $label = null)
     * @method Show\Field|Collection group_type(string $label = null)
     * @method Show\Field|Collection message_type(string $label = null)
     * @method Show\Field|Collection replied_at(string $label = null)
     * @method Show\Field|Collection reply_token(string $label = null)
     * @method Show\Field|Collection social_id(string $label = null)
     * @method Show\Field|Collection text(string $label = null)
     * @method Show\Field|Collection address(string $label = null)
     * @method Show\Field|Collection city(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection use_time(string $label = null)
     * @method Show\Field|Collection result(string $label = null)
     * @method Show\Field|Collection key(string $label = null)
     * @method Show\Field|Collection email_verified_at(string $label = null)
     * @method Show\Field|Collection addition_services(string $label = null)
     * @method Show\Field|Collection after_photos(string $label = null)
     * @method Show\Field|Collection arrive_at(string $label = null)
     * @method Show\Field|Collection before_photos(string $label = null)
     * @method Show\Field|Collection car_type(string $label = null)
     * @method Show\Field|Collection license(string $label = null)
     * @method Show\Field|Collection parking(string $label = null)
     * @method Show\Field|Collection pay_auth_result(string $label = null)
     * @method Show\Field|Collection pay_data(string $label = null)
     * @method Show\Field|Collection pay_result(string $label = null)
     * @method Show\Field|Collection phone(string $label = null)
     * @method Show\Field|Collection suggest_time(string $label = null)
     * @method Show\Field|Collection time(string $label = null)
     * @method Show\Field|Collection worker(string $label = null)
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
