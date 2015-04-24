TPS
===

Theater Presentatie Systeem

Install new theater from Github:

- CHMOD /Applications/XAMPP/xamppfiles/htdocs 777
- Clone the TPS repository from Github to the htdocs folder with the name "narrowcasting"
- This will give you the latest basic software without media files
- Copy any existing media files in the directory:
	- commercial
	- images
	- mededelingen
	- overlay_video
	- script (if you like, but be careful)
	- upload
	- videos
	
	Make sure these directories have "thumb" directories inside of them (except "script")!

- Copy script_reboot.scpt from "resources" to the root
- Copy delete_selected_cue.scpt from "resources" to the root
- Copy the rest of "resources" to "tmp"

- Run "sudo chmod -R 777 /var/www/html/narrowcasting"

- Import latest mysql

