<div class="options_group show_if_braintree-variable-subscription">
<?php
woocommerce_wp_text_input( array ( 
		'label' => sprintf( __( 'Subscription Price (%s)', 'braintree-payments' ), get_woocommerce_currency_symbol( get_woocommerce_currency() ) ), 
		'placeholder' => '0.00', 
		'id' => "variable_subscription_price[$loop]", 
		'name' => "variable_subscription_price[$loop]",
		'wrapper_class' => 'form-row form-row-full',
		'value' => get_post_meta($variation->ID, '_subscription_price', true),
		'desc_tip' => true, 
		'description' => __( 'The price that is billed for the subscription on the period and interval that you assign.', 'braintree-payments' ) 
) );
woocommerce_wp_select( array ( 
		'id' => "variable_subscription_period_interval[$loop}]", 
		'name' => "variable_subscription_period_interval[$loop]",
		'value' => get_post_meta($variation->ID, '_subscription_period_interval', true),
		'label' => 'Billing Interval',
		'wrapper_class' => 'form-row form-row-full',
		'options' => bfwcs_billing_interval_string(), 
		'desc_tip' => true, 
		'description' => __( 'Braintree only allows monthly subscriptions. The frequency can be customized by changing the Braintree plan that is assigned.', 'braintree-payments' ) 
) );
woocommerce_wp_select( array ( 
		'id' => "variable_subscription_length[$loop]", 
		'name' => "variable_subscription_length[$loop]",
		'value' => get_post_meta($variation->ID, '_subscription_length', true),
		'options' => bfwc_subscription_length_string(), 
		'label' => __( 'Length', 'braintree-payments' ),
		'wrapper_class' => 'form-row form-row-full',
		'desc_tip' => true, 
		'description' => __( 'The duration in which the subscription will be active.', 'braintree-payments' ) 
) );
woocommerce_wp_text_input( array ( 
		'label' => __( 'Sign Up Fee', 'braintree-payments' ), 
		'id' => "variable_subscription_sign_up_fee[$loop]",
		'value' => get_post_meta($variation->ID, '_subscription_sign_up_fee', true),
		'placeholder' => '0.00', 
		'name' => "variable_subscription_sign_up_fee[$loop]",
		'wrapper_class' => 'form-row form-row-full',
		'desc_tip' => true, 
		'description' => __( 'If you would like the subscription to have a one time sign up fee, you can add it here.', 'braintree-payments' ) 
) );
woocommerce_wp_text_input( array ( 
		'label' => __( 'Trial Length', 'braintree-payments' ), 
		'id' => "variable_subscription_trial_length[$loop]", 
		'name' => "variable_subscription_trial_length[$loop]",
		'wrapper_class' => 'form-row form-row-full',
		'value' => get_post_meta($variation->ID, '_subscription_trial_length', true),
		'desc_tip' => true, 
		'description' => __( 'The length of the trial associated with the subscription.', 'braintree-payments' ) 
) );

$type = get_post_meta( $variation->ID, '_subscription_trial_length', true ) > 1 ? 'plural' : 'singular';
woocommerce_wp_select( array ( 
		'label' => __( 'Trial Period', 'braintree-payments' ), 
		'id' => "variable_subscription_trial_period[$loop]", 
		'name' => "variable_subscription_trial_period[$loop]",
		'wrapper_class' => 'form-row form-row-full',
		'options' => bfwc_billing_periods_string( $type ), 
		'desc_tip' => true,
		'description' => __('The period in which the trial length is associated with. Braintree accepts days and months as trial periods.', 'braintree-payments' )
) );
?>
	<div class="options_group sandbox">
		<p class="form-field">
			<label>
				<strong><?php _e('Sandbox Data', 'braintree-payments' )?></strong>
			</label>
			<a href="#" data-environment="sandbox"  data-loop="<?php echo $loop?>" type="submit"
				class="button bt-add-plan"><?php _e('Add Sandbox Plan', 'braintree-payments' )?></a>
		</p>
	<?php
	$plans = array ();
	foreach ( $sandbox_plans as $id => $plan ) {
		$plans[ $id ] = sprintf( '%s (%s)', $plan[ 'name' ], $plan[ 'currencyIsoCode' ] );
	}
	
	woocommerce_wp_select( array ( 
			'label' => __( 'Sandbox Braintree Plans', 'braintree-payments' ), 
			'id' => "_sandbox_braintree_plans[$loop]", 
			'name' => "_sandbox_braintree_plans[$loop]", 
			'class' => 'braintree-plans', 
			'options' => $plans, 
			'desc_tip' => true, 
			'description' => __( 'You must assign a Braintree Plan for each currency that you plan on accepting for this subscription.', 'braintree-payments' ) 
	) );
	
	$sandbox_product_plans = get_post_meta( $variation->ID, '_braintree_sandbox_plans', true );
	$has_sand_plans = ( bool ) $sandbox_product_plans;
	?>
		<div class="plan-container">
			<ul class="ul-choices sandbox">
			<?php
			
			if ( $has_sand_plans ) {
				foreach ( $sandbox_product_plans as $currency => $id ) {
					$plan = $sandbox_plans[ $id ];
					?>
					<li class="product-plan">
					<a href="#" class="select2"></a>
					<span><?php printf('%s ( %s )', $plan['name'], $currency)?></span>
					<input type="hidden"
						name="<?php echo "variable_braintree_sandbox_plans[$loop][{$currency}]" ?>" id=""
						value="<?php echo $id?>">
				</li>
					<?php
				}
			}
			?>
			</ul>
		</div>
	</div>

	<?php $account_active = bt_manager ()->get_license_status () === 'active';?>
	<div class="options_group production">
		<p class="form-field">
			<label>
				<strong><?php _e('Production Data', 'braintree-payments' )?></strong>
			</label>
			<a href="#" data-environment="production" data-loop="<?php echo $loop?>" type="submit"
				class="button bt-add-plan <?php if(!$account_active){echo 'disabled';}?>"><?php _e('Add Production Plan', 'braintree-payments' )?></a>
		</p>
	<?php
	$plans = array ();
	
	if ( ! $account_active ) {
		?>
		<p><?php
		
		esc_html( _e( 'Your license is not active. In order to add production Braintree Plans to a subscription product you must have an active license. 
				You can purchase a license <a target="_blank" href="https://wordpress.paymentplugins.com/product-category/braintree-plugins/">Here</a>', 'braintree-payments' ) )?></p>
		<?php
	} else {
		if ( empty( $production_plans ) ) {
			$plans[ 'no-value' ] = __( 'No Production Plans Created.', 'braintree-payments' );
		} else {
			foreach ( $production_plans as $id => $plan ) {
				$plans[ $id ] = sprintf( '%s (%s)', $plan[ 'name' ], $plan[ 'currencyIsoCode' ] );
			}
		}
		
		woocommerce_wp_select( array ( 
				'label' => __( 'Production Braintree Plans', 'braintree-payments' ), 
				'id' => "_production_braintree_plans[$loop}]", 
				'name' => "_production_braintree_plans[$loop}]", 
				'class' => 'braintree-plans', 
				'options' => $plans, 
				'desc_tip' => true, 
				'description' => __( 'You must assign a Braintree Plan for each currency that you plan on accepting for this subscription.', 'braintree-payments' ) 
		) );
	}
	$production_product_plans = get_post_meta( $variation->ID, '_braintree_production_plans', true );
	$has_prod_plans = ( bool ) $production_product_plans;
	?>
		<div class="plan-container">
			<ul class="ul-choices production">
					<?php
					
					if ( $has_prod_plans ) {
						foreach ( $production_product_plans as $currency => $id ) {
							$plan = $production_plans[ $id ];
							?>
					<li class="product-plan">
					<a href="#" class="select2"></a>
					<span><?php printf('%s ( %s )', $plan['name'], $currency)?></span>
					<input type="hidden"
						name="<?php echo "variable_braintree_production_plans[$loop][{$currency}]" ?>" id=""
						value="<?php echo $id?>">
				</li>
					<?php
						}
					}
					?>
			</ul>
		</div>
	</div>
</div>