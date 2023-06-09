# FreeHelp

FreeHelp makes FreeScout look and feel more like Help Scout, adding many small quality-of-life improvements.

- Shift-click checkboxes to select a range of conversations at once
- Convert Dropdown-type Custom Fields to Select2 inputs
- Type `/` to quickly access search
- Type `Escape` to close dropdowns, cancel editing conversation titles, and cancel editing a note
- Apply many CSS tweaks to make FreeScout look and feel more like Help Scout

## Installation

These instructions assume you installed FreeScout using the [recommended process](https://github.com/freescout-helpdesk/freescout/wiki/Installation-Guide), the "one-click install" or the "interactive installation bash-script", and you are viewing this page using a macOS or Ubuntu system.

Other installations are possible, but not supported here.

1. Download the [latest release of FreeScout Dropkick JS](https://github.com/fulldecent/freescout-dropkick-js/releases).

2. Unzip the file locally.

3. Open DropkickJSServiceProvider.php using a code editor and change between the `<<<JS` and `JS` lines to remove my hack use case and add your own.

4. Copy the folder into your server using SFTP.

   ```sh
   scp -r ~/Desktop/freehelp-root@freescout.example.com:/var/www/html/Modules/FreeHelp/
   ```

5. SSH into the server and update permissions on that folder.

   ```sh
   chown -r www-data:www-data /var/www/html/Modules/FreeHelp/
   ```

6. Access your admin modules page like https://freescout.example.com/modules/list.

7. Find **FreeHelp** and click ACTIVATE.

## Inspiration

* This project was inspired by [DropkickJS](https://github.com/fulldecent/freescout-dropkick-js).
