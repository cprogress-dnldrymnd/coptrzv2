<?php
/**
 * Plugin Name: WPML Data Eraser Utility
 * Description: Advanced AJAX-powered utility to safely drop tables and remove metadata associated with the WPML plugin, featuring live progress tracking.
 * Version: 2.0.0
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the administration menu page under the 'Tools' top-level menu.
 *
 * @return void
 */
function dd_wpml_eraser_menu() {
	add_management_page(
		'WPML Eraser',
		'WPML Eraser',
		'manage_options',
		'dd-wpml-eraser',
		'dd_wpml_eraser_page_html'
	);
}
add_action( 'admin_menu', 'dd_wpml_eraser_menu' );

/**
 * Renders the HTML for the WPML Eraser administration interface.
 * Contains the execution warnings, progress bar container, and live log output window.
 *
 * @return void
 */
function dd_wpml_eraser_page_html() {
	?>
	<div class="wrap">
		<h1>WPML Data Eraser Utility</h1>
		<div class="notice notice-warning inline">
			<p><strong>CRITICAL WARNING:</strong> This tool performs destructive database queries. It will permanently delete WPML tables and metadata. Ensure you have a complete database backup before proceeding.</p>
		</div>

		<div style="margin-top: 20px; max-width: 800px;">
			<button id="dd-start-erase" class="button button-primary button-hero">Initialize Wipe Protocol</button>
			
			<div id="dd-progress-container" style="display:none; margin-top: 20px;">
				<h3 id="dd-status-text">Processing...</h3>
				<div style="width: 100%; background: #dedede; border-radius: 3px; height: 24px; overflow: hidden;">
					<div id="dd-progress-bar" style="width: 0%; height: 100%; background: #2271b1; transition: width 0.3s ease;"></div>
				</div>
				<pre id="dd-log-output" style="background: #111; color: #0f0; padding: 15px; border-radius: 4px; height: 300px; overflow-y: scroll; margin-top: 15px; font-family: monospace;"></pre>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Enqueues the necessary JavaScript into the admin footer exclusively for the eraser page.
 * Handles the async fetching of tasks and sequential execution via WordPress AJAX API.
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function dd_wpml_eraser_scripts( $hook ) {
	if ( $hook !== 'tools_page_dd-wpml-eraser' ) {
		return;
	}
	?>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			const startBtn = document.getElementById('dd-start-erase');
			const progressContainer = document.getElementById('dd-progress-container');
			const progressBar = document.getElementById('dd-progress-bar');
			const logOutput = document.getElementById('dd-log-output');
			const statusText = document.getElementById('dd-status-text');

			/**
			 * Appends a timestamped message to the visual log window.
			 *
			 * @param {string} message The text to display in the log.
			 */
			function logMessage(message) {
				const time = new Date().toLocaleTimeString();
				logOutput.innerHTML += `[${time}] ${message}\n`;
				logOutput.scrollTop = logOutput.scrollHeight;
			}

			startBtn.addEventListener('click', async function() {
				if (!confirm('Are you absolutely sure? This action is irreversible.')) {
					return;
				}

				startBtn.disabled = true;
				progressContainer.style.display = 'block';
				logMessage('Initializing sequence... mapping database targets.');

				try {
					// Step 1: Retrieve the blueprint of tasks
					const formData = new FormData();
					formData.append('action', 'dd_wpml_get_tasks');
					formData.append('security', '<?php echo wp_create_nonce( "dd_wpml_ajax_nonce" ); ?>');

					const response = await fetch(ajaxurl, { method: 'POST', body: formData });
					const data = await response.json();

					if (!data.success) {
						throw new Error(data.data || 'Failed to retrieve task list.');
					}

					const tasks = data.data;
					logMessage(`Found ${tasks.length} targets for deletion.`);

					// Step 2: Execute tasks sequentially
					for (let i = 0; i < tasks.length; i++) {
						const task = tasks[i];
						logMessage(`Executing: Target -> ${task.target}`);

						const taskData = new FormData();
						taskData.append('action', 'dd_wpml_execute_task');
						taskData.append('security', '<?php echo wp_create_nonce( "dd_wpml_ajax_nonce" ); ?>');
						taskData.append('task_type', task.type);
						taskData.append('task_target', task.target);

						const taskResponse = await fetch(ajaxurl, { method: 'POST', body: taskData });
						const taskResult = await taskResponse.json();

						if (taskResult.success) {
							logMessage(`Success: ${taskResult.data}`);
						} else {
							logMessage(`ERROR: ${taskResult.data}`);
						}

						// Update Progress
						const percent = Math.round(((i + 1) / tasks.length) * 100);
						progressBar.style.width = percent + '%';
						statusText.innerText = `Processing... ${percent}%`;
					}

					statusText.innerText = 'Wipe Complete';
					logMessage('All operations finished successfully. You may now deactivate and delete this utility plugin.');

				} catch (error) {
					logMessage(`FATAL ERROR: ${error.message}`);
					statusText.innerText = 'Process halted due to error.';
				}
			});
		});
	</script>
	<?php
}
add_action( 'admin_enqueue_scripts', 'dd_wpml_eraser_scripts' );

/**
 * AJAX handler: Scans the database and generates an array of specific deletion tasks.
 * Builds an array containing individual tables and distinct meta deletion steps.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @return void
 */
function dd_wpml_ajax_get_tasks() {
	check_ajax_referer( 'dd_wpml_ajax_nonce', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Insufficient permissions.' );
	}

	global $wpdb;
	$tasks = [];

	// Find all tables prefixing with WPML's 'icl_'
	$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}icl_%'" );
	if ( ! empty( $tables ) ) {
		foreach ( $tables as $table ) {
			$tasks[] = [
				'type'   => 'table',
				'target' => $table
			];
		}
	}

	// Add static tasks for meta and options cleanup
	$tasks[] = [ 'type' => 'options', 'target' => 'wpml_options' ];
	$tasks[] = [ 'type' => 'postmeta', 'target' => 'wpml_postmeta' ];
	$tasks[] = [ 'type' => 'usermeta', 'target' => 'wpml_usermeta' ];
	$tasks[] = [ 'type' => 'termmeta', 'target' => 'wpml_termmeta' ];

	wp_send_json_success( $tasks );
}
add_action( 'wp_ajax_dd_wpml_get_tasks', 'dd_wpml_ajax_get_tasks' );

/**
 * AJAX handler: Executes a single specific task provided by the client.
 * Features strict table validation to prevent arbitrary SQL injection drops.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @return void
 */
function dd_wpml_ajax_execute_task() {
	check_ajax_referer( 'dd_wpml_ajax_nonce', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Insufficient permissions.' );
	}

	global $wpdb;
	$type   = isset( $_POST['task_type'] ) ? sanitize_text_field( $_POST['task_type'] ) : '';
	$target = isset( $_POST['task_target'] ) ? sanitize_text_field( $_POST['task_target'] ) : '';

	switch ( $type ) {
		case 'table':
			// SECURITY GUARD: Ensure the table exactly matches the expected prefix and pattern to prevent malicious drops.
			if ( strpos( $target, $wpdb->prefix . 'icl_' ) === 0 ) {
				$wpdb->query( "DROP TABLE IF EXISTS `$target`" );
				wp_send_json_success( "Dropped table: {$target}" );
			} else {
				wp_send_json_error( "Invalid table target: {$target}" );
			}
			break;

		case 'options':
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%wpml_%' OR option_name LIKE '%icl_%'" );
			wp_send_json_success( "Cleared WPML options." );
			break;

		case 'postmeta':
			$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '%wpml_%' OR meta_key LIKE '%icl_%'" );
			wp_send_json_success( "Cleared WPML post metadata." );
			break;

		case 'usermeta':
			$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%wpml_%' OR meta_key LIKE '%icl_%'" );
			wp_send_json_success( "Cleared WPML user metadata." );
			break;

		case 'termmeta':
			$wpdb->query( "DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE '%wpml_%' OR meta_key LIKE '%icl_%'" );
			wp_send_json_success( "Cleared WPML term metadata." );
			break;

		default:
			wp_send_json_error( "Unknown task type." );
			break;
	}
}
add_action( 'wp_ajax_dd_wpml_execute_task', 'dd_wpml_ajax_execute_task' );