<div class="wrap">
    <h1 class="wp-heading-inline">Delivery Drivers</h1>

    <h2 style="margin-bottom: 0px;margin-top: 28px;">Orders on the way
    </h2>
    <br>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <tr>
                <th class="manage-column column-primary ">Order</th>
                <th>Status</th>
                <th>Driver</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $orders = wc_get_orders(array(
                'limit'        => -1, // Query all orders
                'status' => array('wc-processing', 'wc-on-hold'),
                'meta_key'     => 'skye_order_driver', // The postmeta key field
                'meta_compare' => 'EXISTS', // The comparison argument
            ));
            $count = 0;
            foreach ($orders as $order) {
                $count++;
            ?>
                <tr>
                    <td class="title column-title has-row-actions column-primary" data-colname="Driver">
                        <b>
                        <a href="post.php?post=<?php echo $order->get_id(); ?>&action=edit">
                            #<?php echo $order->get_id(); ?> 
                            <?php 
								$customer = new WC_Customer( $order->get_user_id() );
								echo $customer->get_display_name();
							?>
                        </a>
                        </b>
                    </td>
                    <td><?php echo $order->get_status(); ?></td>
                    <td>
                        <?php
                        $driver_id = $order->get_meta("skye_order_driver");
                        if ($driver_id) {
                            $user = get_user_by('ID', $driver_id);
                            if ($user) {
                                echo "<a href='user-edit.php?user_id=" . $user->ID . "'><b>" . $user->display_name . "</b></a>";
                            }
                        }
                    ?></td>
                    <td data-colname="date"><?php 
                    $date = getdate(strtotime($order->get_date_modified()));
                    echo $date['month'] .  ' ' . $date['mday'] . ', ' . $date['year'];
                    ?></td>
                </tr>
            <?php } ?>
            <?php if ($count == 0) { ?>
                <tr>
                <td colspan="4" style="text-align: center;">No orders</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="title column-title has-row-actions column-primary"><?php echo $count; ?> Order(s)</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <h2 style="margin-bottom: 0px;margin-top: 28px;">Orders without drivers
    </h2>
    <br>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <tr>
                <th class="manage-column column-primary ">Order</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $orders = wc_get_orders(array(
                'limit'        => -1, // Query all orders
                'status' => array('wc-processing', 'wc-on-hold'),
                'meta_key'     => 'skye_order_driver', // The postmeta key field
                'meta_compare' => 'NOT EXISTS', // The comparison argument
            ));
            $count = 0;
            foreach ($orders as $order) {
                $count++;
            ?>
                <tr>
                    <td class="title column-title has-row-actions column-primary" data-colname="Driver">
                        <b>
                        <a href="post.php?post=<?php echo $order->get_id(); ?>&action=edit">
                            #<?php echo $order->get_id(); ?> 
                            <?php 
								$customer = new WC_Customer( $order->get_user_id() );
								echo $customer->get_display_name();
							?>
                        </a>
                        </b>
                    </td>
                    <td><?php echo $order->get_status(); ?></td>
                    <td data-colname="date"><?php 
                    $date = getdate(strtotime($order->get_date_modified()));
                    echo $date['month'] .  ' ' . $date['mday'] . ', ' . $date['year'];
                    ?></td>
                </tr>
            <?php } ?>
            <?php if ($count == 0) { ?>
                <tr>
                <td colspan="3" style="text-align: center;">No orders</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="title column-title has-row-actions column-primary"><?php echo $count; ?> Order(s)</td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>


    <h2 style="margin-bottom: 0px;margin-top: 28px;">Active drivers
        <a href="user-new.php" class="page-title-action">Add new driver</a>
    </h2>
    <br>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <tr>
                <th class="manage-column column-primary ">Drivers
                </th>
                <th>Phone
                </th>
                <th>Email
                </th>
                <th>Address
                </th>
                <th>Active Orders
                </th>
            </tr>
        </thead>
        <tbody>
            <?php

            $drivers = get_users(array('role' => 'skye_delivery_driver'));
            $count = 0;
            foreach ($drivers as $driver) {
                $count++;
            ?>
                <tr>
                    <td class="title column-title has-row-actions column-primary" data-colname="Driver">
                        <a href="user-edit.php?user_id=<?php echo $driver->ID; ?>"><?php echo $driver->display_name; ?></a>
                    </td>
                    <td data-colname="Phone"><a href="tel:<?php echo get_user_meta($driver->ID, 'billing_phone', true); ?>"><?php echo get_user_meta($driver->ID, 'billing_phone', true); ?></a></td>
                    <td data-colname="Email"><a href="mailto:<?php echo $driver->user_email; ?>"><?php echo $driver->user_email; ?></a></td>
                    <td data-colname="Address">
                        <?php echo get_user_meta($driver->ID, 'billing_address_1', true); ?>
                        <?php echo get_user_meta($driver->ID, 'billing_address_2', true); ?>
                        <?php echo get_user_meta($driver->ID, 'billing_city', true); ?>
                        <?php echo get_user_meta($driver->ID, 'billing_state', true); ?>
                        <?php echo get_user_meta($driver->ID, 'billing_country', true); ?>
                    </td>
                    <td>
                        <?php
                        $orders = wc_get_orders(array(
                            'limit'        => -1, // Query all orders
                            'status' => array('wc-processing'),
                            'meta_key'     => 'skye_order_driver', // The postmeta key field
                            'meta_value'     => $driver->ID, // The postmeta key field
                            'meta_compare' => 'EXISTS', // The comparison argument
                        ));
                        echo count($orders);

                        ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($count == 0) { ?>
                <tr>
                <td colspan="5" style="text-align: center;">No Driver</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="title column-title has-row-actions column-primary"><?php echo $count; ?> Drivers</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</div>