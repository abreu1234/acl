# Copyright (c) Rafael Abrei. (http://www.rafaelabreu.eti.br)
#
# Licensed under The MIT License
# For full copyright and license information, please see the LICENSE.txt
# Redistributions of files must retain the above copyright notice.
# MIT License (http://www.opensource.org/licenses/mit-license.php)

CREATE TABLE permission (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    prefix VARCHAR(100),
    controller VARCHAR(20) NOT NULL,
    action VARCHAR(100) NOT NULL,
    unique_string VARCHAR(130) NOT NULL UNIQUE
);

CREATE TABLE user_group_permission (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    group_or_user VARCHAR(20) NOT NULL,
    group_or_user_id INT(11) NOT NULL,
    permission_id INT(11) NOT NULL,
    allow BOOLEAN DEFAULT 0,
    CONSTRAINT fk_permission FOREIGN KEY (permission_id)
    REFERENCES permission(id)
    ON DELETE CASCADE
);