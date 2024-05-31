<style>
	table {
		border-collapse: collapse;
		width: 100%;
	}
	table th,table td {
		border: 1px solid;
		padding: 10px;
	}
	img {
		max-width: 100%;
	}
</style>

<h1><strong>Documentation</strong></h1>


<h3>Logo and Phone number(Company Details)</h3>


<p>
	Logos and phone number can be edited <a target="_blank" href="https://trula.co.uk/wp-admin/themes.php?page=crb_carbon_fields_container_company_details.php">here</a>.
</p>

<table>
	<tr>
		<th>Name</th>
		<th>Description</th>
	</tr>
	<tr>
		<td>
			<strong><code>Logo</code></strong>
		</td>
		<td>
			<p>
				This is the primary logo that will display beside the partnership logo.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Alt Logo</code></strong>
		</td>
		<td>
			<p>
				This is the logo that will display on the sticky header.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Partner Logo</code></strong>
		</td>
		<td>
			<p>
				This is the partner logo the will display beside the primary logo
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Footer Logo</code></strong>
		</td>
		<td>
			<p>
				This is the logo that will display on the footer.
			</p>
		</td>
	</tr>
</table>

<br>
<hr>
<br>


<h3>Modules/Section</h3>


<p>
	These are the reusable blocks that can use when creating pages.
</p>

<table>
	<tr>
		<th>Image</th>
		<th style="width: 300px">Description</th>
	</tr>
	<tr>
		<td>
			<img src="<?= get_stylesheet_directory_uri() ?>/admin/images/module-1.jpg">
		</td>
		<td>
			<p>
				Please select <strong>modules</strong> as the page template.
			</p>
			<p>
				After selecting the module you can now add module by clicking add entry.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<img src="<?= get_stylesheet_directory_uri() ?>/admin/images/module-2.jpg">
		</td>
		<td>
			<p>
				After adding a module. You can now see the custom fields which you can add your contents.
			</p>
			<p>
				Each module has their style fields. 
			</p>

			
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p>Using Style Fields</p>
			<table>
				<tr>
					<th>
						<strong>Style</strong>
					</th>
					<th>
						<strong>Description</strong>
					</th>
				</tr>
				<tr>
					<td>
						<strong>Container</strong>
					</td>
					<td>
						<code>Default</code> - Max width is 1450px.
						<br>
						<br>
						<code>Full Width</code> - Max width is 100%.
						<br>
						<br>
						<code>Small Container</code> - Max width is 1104px.
						<br>
						<br>
						<code>Extra Small Container</code> - Max width is 860px.
					</td>
				</tr>
				<tr>
					<td>
						<strong>Text Align</strong>
					</td>
					<td>
						<code>Text Left</code> - Text alignment will be at left(default).
						<br>
						<br>
						<code>Text Center</code> - Text alignment will be at the center.
						<br>
						<br>
						<code>Text Right</code> - Text alignment will be at the right.
						<br>
						<br>
						<code>Text Justify</code> - Text alignment will be justified.
					</td>
				</tr>
				<tr>
					<td>
						<strong>Padding Top/Bottom</strong>
					</td>
					<td>
						<code>None</code> - No padding.
						<br>
						<br>
						<code>Extra Small</code> - Padding is 35px;
						<br>
						<br>
						<code>Small</code> - Padding is 48px.
						<br>
						<br>
						<code>Medium</code> - Padding is 64px.
						<br>
						<br>
						<code>Large</code> - Padding is 90px.
						<br>
						<br>
						<code>Extra Large</code> - Padding is 128px;
					</td>
				</tr>
				<tr>
					<td>
						<strong>Background</strong>
					</td>
					<td>
						<code>None</code> - No Background
						<br>
						<br>
						<code>Custom Color</code> - Set your desired color as background.
						<br>
						<br>
						<code>Image</code> - Set your desired image as background.
						<br>
						<br>
						<code>Background Primary</code> - Background color is <span style="background-color: #000545;padding: 10px; color: #fff;display: inline-block;">#000545</span>
						<br>
						<br>
						<code>Background Secondary</code> - Background color is <span style="background-color: #01006c;padding: 10px; color: #fff;display: inline-block;">#01006c</span>
						<br>
						<br>
						<code>Background Accent</code> - Background color is <span style="background-color: #ff5003;padding: 10px; color: #fff;display: inline-block;">#ff5003</span>
					</td>
				</tr>

			</table>
		</td>
	</tr>

</table>

<br>
<hr>
<br>

<h3>General Settings</h3>


<p>
	General settings can be edited <a target="_blank" href="https://trula.co.uk/wp-admin/themes.php?page=crb_carbon_fields_container_general_settings.php">here</a>.
</p>

<table>
	<tr>
		<th>Name</th>
		<th>Description</th>
	</tr>
	<tr>
		<td>
			<strong><code>Default Page Banner</code></strong>
		</td>
		<td>
			<p>
				Set the default page banner background image when featured image is empty.
			</p>
		</td>
	</tr>
</table>

<br>
<hr>
<br>

<h3>After Banner</h3>
<p>
	These are the templates that will appear directly below the page banner.
</p>
<p>
	You have the option to hide this on specific page. Just tick on the on items on the After Banner Options at the right side of the page when editing.
</p>
<p>
	After Banner can be edited <a target="_blank" href="https://trula.co.uk/wp-admin/themes.php?page=crb_carbon_fields_container_after_banner.php">here</a>.
</p>

<br>
<hr>
<br>

<h3>Before Footer</h3>
<p>
	These are the templates that will appear above the footer.
</p>
<p>
	You have the option to hide this on specific page. Just tick on the on items on the Before Footer Options at the right side of the page when editing.
</p>

<p>
	Before Footer can be edited <a target="_blank" href="https://trula.co.uk/wp-admin/themes.php?page=crb_carbon_fields_container_before_footer.php">here</a>.
</p>

<br>
<hr>
<br>

<h3>Footer Settings</h3>
<p>
	You can manage the footer contents here.
</p>
<p>
	Footer Settings can be edited <a target="_blank" href="https://trula.co.uk/wp-admin/themes.php?page=crb_carbon_fields_container_footer_settings.php">here</a>.
</p>


<table>
	<tr>
		<th>Name</th>
		<th>Description</th>
	</tr>
	<tr>
		<td>
			<strong><code>FOOTER CTA</code></strong>
		</td>
		<td>
			<p>
				This is the CTA the will display on all pages. 
			</p>
			<p>
				You have the option to hide this on specific page. Just tick on the Hide <code>Hide Footer CTA</code> on the Page Options at the right side when editing the page.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Footer Text</code></strong>
		</td>
		<td>
			<p>
				This is the text that appears at the bottom of the footer.
			</p>
		</td>
	</tr>
</table>

<br>
<hr>
<br>

<h3>Header and Footer Scripts</h3>
<p>
	This are the additional scripts that will be print either on footer or header.
</p>
<p>
	Header and Footer Scripts can be edited <a target="_blank" href="https://trula.co.uk/wp-admin/themes.php?page=crb_carbon_fields_container_header_and_footer_scripts.php">here</a>.
</p>


<table>
	<tr>
		<th>Name</th>
		<th>Description</th>
	</tr>
	<tr>
		<td>
			<strong><code>FOOTER CTA</code></strong>
		</td>
		<td>
			<p>
				This is the CTA the will display on all pages. 
			</p>
			<p>
				You have the option to hide this on specific page. Just tick on the Hide <code>Hide Footer CTA</code> on the Page Options on right side when editing the page.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Footer Text</code></strong>
		</td>
		<td>
			<p>
				This is the text that appears at the bottom of the footer.
			</p>
		</td>
	</tr>
</table>

<br>
<hr>
<br>

<h3>Templates</h3>

<p>
	Templates are sections that can be globally display on every pages.
</p>
<p>
	You can manage the templates by going <a href="https://trula.co.uk/wp-admin/edit.php?post_type=templates" target="_blank">here</a>.
</p>

<table>
	<tr>
		<th>Image</th>
		<th style="width: 300px;">Description</th>
	</tr>
	<tr>
		<td>
			<img src="<?= get_stylesheet_directory_uri() ?>/admin/images/templates.jpg">
		</td>
		<td>
			<p>
				To use a template on a page click add entry then select <strong>templates</strong>.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<img src="<?= get_stylesheet_directory_uri() ?>/admin/images/template-select.jpg">
		</td>
		<td>
			<p>
				Add template title and select template from the list.
			</p>
			<p>
				Published the changes and the template will automatically appear on the frontend.
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Partner Logo</code></strong>
		</td>
		<td>
			<p>
				This is the partner logo the will display beside the primary logo
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<strong><code>Footer Logo</code></strong>
		</td>
		<td>
			<p>
				This is the logo that will display on the footer.
			</p>
		</td>
	</tr>
</table>