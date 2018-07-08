=== File Manager ===
Contributors: aihimel
Donate link: http://www.giribaz.com/
Tags: file manager, wordpress file manager, wp file manager, FTP, elfinder, file Browser, manage files, upload, delete, rename, copy, move, online file browser, remote file manager, drag and drop, folder upload
Requires at least: 3.8+
Tested up to: 4.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Upload, delete, copy, move, rename, edit, compress, extract files. You don't need to worry about ftp any more. It is realy simple and easy to use.

== Description ==

Most robust and powerful file manager for wordpress. You can upload, delete, copy, move, rename, archive, extract files. You don't need to worry about ftp any more. It is realy simple and easy to use.
Just install the plugin following standard wordpress plugin install process and visit your dashbord. You will find a side menu called file manager. Just click on it to open file manager.

> <strong>[Extend File Manager](http://www.giribaz.com/)</strong> with tons of features and priority support.

<strong>[Documentation](http://www.giribaz.com/file-manager-documentation/)</strong> See detail documentation here.

<strong>[Get Express Support](http://www.giribaz.com/support/)</strong> Open a support ticket to get support quickly.

[youtube https://www.youtube.com/watch?v=93aiNIWRLqY]

= Key Features =

+ Upload, Download and Delete operations
+ All operations with files and folders (copy, move, upload, create folder/file, rename, archive, extract, edit, etc.)
+ Light and Elegant client UI
+ Drag & Drop file upload support
+ List and Icons view available
+ Alternative to FTP
+ Archives create/extract (zip, rar, 7z, tar, gzip, bzip2)
+ Image and File Edit Support
+ Quicklook, preview for common file types
+ Calculate directory sizes
+ Video and audio preview/play
+ Support file search and sort

= Extended Features =

+ High performance server backend
+ Uses local file system no need of database.
+ Keyboard shortcuts available
+ Multiple file/folder selection
+ Move/Copy files with Drag & Drop
+ Rich context menu and toolbar
+ Thumbnails for image files
+ Auto resize on file upload.
+ UTF-8 Normalizer of file-name and file-path etc.
+ Sanitizer of file-name and file-path etc.
+ Folder upload (supports on google chrome/Chromium)
+ Chunked file upload
+ Upload directly to the folder
+ Creating the archive by specifying the file name
+ File browsing history
+ Responsive(Works on tablet and phone)


= Extend File Manager =

+ **Frontend:** Enable file manager plugin for frontend.
+ **Shortcode Support:** Shortcode support for file manager to post it anywhere on your website.
+ **Personal User Folder:** Every user has personal/private folder.
+ **User Role Permission:** Set permission for user roles.
+ **User Permission:** Set permission for specific users.
+ **File Type:** Control what files can be uploaded and what file can be downloaded.
+ **File Size:** Control maximum file size that can be uploaded.
+ **Maximum Operations:** Support 8 types of file operation control.
+ **Ban Roles/Users:** Ban Users and Roles.

**[Extend File Manager](http://www.giribaz.com/)**

== Installation ==

= Requirements =

+ At least Firefox 12, Internet Explorer 9, Safari 6, Opera 12 or Chrome/Chromium 19
+ PHP 5.2+ (for thumbnails - mogrify utility or GD/Imagick module)
+ DISALLOW_FILE_EDIT must be false to edit files from file manager.

= Installation process =

+ Upload and install the plugin.
+ Go to admin dashbord
+ Click on File Manager side menu

**Congratulations** you have done it!


== Frequently Asked Questions ==

= Invalid backend configuration. Readable volumes not available. =

Please check your file permission from your hosting panel. The permission must be 0755 for file/folder. If you are using a vps(virtual private server) then you must ensure that the owner of your installation is PHP aka www-data

= I can not upload larger files then *MB =

Please check your maximum file upload limit on hosting. You must increase the post_max_size from your hosting to upload larger files.

= Will I support the plugin? =

Yes, I will support the plugin.


== Screenshots ==

1. Overall View
2. Tool Bar
3. Advanced Search
4. File Size Indecator
5. Image Preview
6. Edit Image
7. Preview file
8. Edit file
9. Right Click Menu
10. File manager settings page
11. File manager pro widget(PRO only)
12. File manager pro shortcode(PRO only)
13. File manager pro frontend(PRO only)

== Changelog ==

= 5.0.0 (12 September, 2017) =

* DISALLOW_FILE_EDIT reporting added [disallow_file_edit has to be false](https://wordpress.org/support/topic/disallow_file_edit-has-to-be-false/)
* PHP 7 double underscore(__) warning fixed [PHP issues](https://wordpress.org/support/topic/php-issues-11/)
* Undifined index warning fixed [Notice: Undefined index in debug mode](https://wordpress.org/support/topic/notice-undefined-index-in-debug-mode/)
* Control Hide width of file manager

= 4.1.6 (10 Jun, 2017) =

* Empty Downloaded file fixed
* Non-ASCII character support for file/folder name
* Tested on WordPress 4.8

= 4.1.4 (2 March, 2017) = 

* Lanugage option added
* Designe issue fixed

= 4.1.3 (19 February, 2017) =

* Lower version of PHP error fixed

= 4.1.2 (5 January, 2017) =

* Extra slash issue solved

= 4.1.1 (14 December, 2016) =

* Minor UI changes

= 4.1.0 =

* UI changed
* Server configuration panel added
* fm_options hook added

= 4.0.4 =

* OS independent file path structure
* Logging system added

= 3.0.0 =

* Bug fixed and Interface updated.

= 2.2.4 =

* Data is not valid problem fixed.

= 2.2.3 =

* Chromium design issue fixed.

= 2.2.2 =

* Extra character output fixed.

= 2.2.1 =

* Page speed optimized
* Security Updated

= 2.2.0 =

* Extra slash problem on file edit has been checked.
* Security update.

= 2.0.1 =

* Activation error fixed with PHP 5.2

= 2.0 =

* Internal structure updated.

= 1.0 =

* Initial release of the plugin.
