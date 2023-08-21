# MarketStorm Wordpress Plugin

Wordpress plugin for Marketstorm Pixels.

## Features

- Multisite and Single site Wordpress instances
- Compatible with Matomo Tag Manager Pixels
- Prevent Pixel deferral by Nitropack, Jetpack, or Cloudflare
- One click updates

## Installation and Usage

**IMPORTANT:** If the site already has a Marketstorm Pixel placed, the Tag Manager code must be removed prior to following the steps below

1) **Download** the latest release from [here](https://github.com/marketstorm-ai/wordpress-plugin/releases/latest)
   - Choose *Source code (zip)* under Assets tab

3) **Upload** Plugin to Wordpress
   - Plugins -> Add New -> Upload Plugin


![](/docs/wordpress_upload.png)


**WARNING**: Network Activate will activate the plugin for all sites on a Wordpress Multisite Instance. Unless that is the goal, the relevant sites should be activated manually.

3) **Activate** the plugin for the one site

4) Find the Container ID for the Tag Manager in Matomo
   
   - Tag Manager -> Tag Manager (Sidebar) -> Manage Containers

![](/docs/container_id.png)

6) On Wordpress, enter the Matomo Container ID for the Tag Manager and press **Save**
   
   - Once saved, a popup with the text "Settings saved." will appear

   - Settings -> Marketstorm

![](/docs/wordpress_saved.png)

7) If needed, a plugin update check can be triggered using the Update link on the MarketStorm entry of the Plugin List.

   - Can be found next to the plugin's Activate/Deactivate buttons

## Important Notes
- The Container ID stored by the plugin will not persist upon deactivation
- If the Container ID is left blank, no code will be placed in the client site.
