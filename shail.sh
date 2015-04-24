#!/bin/bash

tell application "QLab"
                         tell front workspace
make type "Video"
                            set newCue to last item of (selected as list)
                            set newFileTarget to "/var/www/html/narrowcasting/videos/Sunset.jpg"
                            set file target of newCue to newFileTarget
                            set q name of newCue to "Winter.jpg"
                            set post wait of newCue to 10 
                            set continue mode of newCue to auto_continue 
                            make type "Stop"
                            set newStopCue to last item of (selected as list)
                            set cue target of newStopCue to newCue 
                            set q name of newStopCue to "Winter.jpg" 
                            set continue mode of newStopCue to auto_continue
make type "Video"
                            set newCue to last item of (selected as list)
                            set newFileTarget to "/var/www/html/narrowcasting/videos/Sunset.jpg"
                            set file target of newCue to newFileTarget
                            set q name of newCue to "Water lilies.jpg"
                            set post wait of newCue to 10 
                            set continue mode of newCue to auto_continue 
                            make type "Stop"
                            set newStopCue to last item of (selected as list)
                            set cue target of newStopCue to newCue 
                            set q name of newStopCue to "Water lilies.jpg" 
                            set continue mode of newStopCue to auto_continue
make type "Video"
                            set newCue to last item of (selected as list)
                            set newFileTarget to "/var/www/html/narrowcasting/videos/Sunset.jpg"
                            set file target of newCue to newFileTarget
                            set q name of newCue to "Sunset.jpg"
                            set post wait of newCue to 10 
                            set continue mode of newCue to auto_continue 
                            make type "Stop"
                            set newStopCue to last item of (selected as list)
                            set cue target of newStopCue to newCue 
                            set q name of newStopCue to "Sunset.jpg" 
                            set continue mode of newStopCue to auto_continue
start cue "1"
                          end tell

                          end tell
