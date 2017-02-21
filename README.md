# User Activity Tracking

A simple alternative to WordPress user management that includes user activity monitoring. If a user attempts to download an image or document a login screen will appear requiring them to enter their first & last name, organization and email address. The user will also be required to accept a terms of use agreement (optional).

### Note:
This plugin is still in it's very early stages and is not recommended for use on any production environment.


#### To Do:
1. Add wp-admin options pages:
	* Ability to add Terms of Service document + content within WYSIWYG editor
	* Ability to add form title & introduction
	* Ability to choose which form fields are present
		* Will require changing DB tables to allow `NULL` values.
	* Ability to choose to include Bootstrap forms & modal windows within plugin styles
		* Eventually add ability to add custom styles.
	* Ability to choose what kind of documents to "gate"
		* PDF, DOCX, RTF, HTML, JPG, PNG, GIF, etc..
2. Build out User Activity Tracking overview page (currently dummy data)
3. Add sorting/searching functions to Users & Downloads page.
