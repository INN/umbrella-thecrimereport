<?php
// Member Table Header
function crimereport_rcp_members_page_table_header() {
	echo '<th scope="col" class="rcp-role-col manage-column">Mailchimp Opt-In</th>';
}
add_action( 'rcp_members_page_table_header', 'crimereport_rcp_members_page_table_header' );
add_action( 'rcp_members_page_table_footer', 'crimereport_rcp_members_page_table_header' );

// Member Table Row
function crimereport_rcp_members_page_table_column( $member_id ) {
	$mailchimp_optin = get_user_meta( $member_id, 'rcp_subscribed_to_mailchimp', true ) ? 'Yes' : '';
	echo '<td data-colname="Mailchimp Signup">' . $mailchimp_optin . '</td>';
}
add_action( 'rcp_members_page_table_column', 'crimereport_rcp_members_page_table_column' );
