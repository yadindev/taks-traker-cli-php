#!/bin/bash

if ! grep -q "alias test-cli=" ~/.bashrc; then
    echo "Adding alias to ~/.bashrc"
    echo "alias task-cli='php $(pwd)/task-cli.php'" >> ~/.bashrc
    echo "Alias added, please run: 'source ~/.bashrc' "
    echo "And then use 'task-cli' to run app."
    read -p "Press Enter to exit this script..."
else 
    echo "Alias already exists in ~/.bashrc"
fi