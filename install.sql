# Pepperjam Exporter

SET @configuration_group_id=0;
SELECT @configuration_group_id:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'Pepperjam Exporter Configuration'
LIMIT 1;
DELETE FROM configuration WHERE configuration_group_id = @configuration_group_id;
DELETE FROM configuration_group WHERE configuration_group_id = @configuration_group_id;

INSERT INTO configuration_group (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (NULL, 'Pepperjam Exporter Configuration', 'Set Pepperjam Options', '1', '1');
SET @configuration_group_id=last_insert_id();
UPDATE configuration_group SET sort_order = @configuration_group_id WHERE configuration_group_id = @configuration_group_id;

INSERT INTO configuration (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES 
(NULL, 'Output File Name', 'PEPPERJAM_OUTPUT_FILENAME', 'pepperjam', 'Set the name of your Pepperjam output file', @configuration_group_id, 0, NOW(), NULL, NULL),
(NULL, 'Output Directory', 'PEPPERJAM_DIRECTORY', 'feed/pepperjam/', 'Set the name of your Pepperjam output directory', @configuration_group_id, 0, NOW(), NULL, NULL),

(NULL, 'Use cPath in url', 'PEPPERJAM_USE_CPATH', 'false', 'Use cPath in product info url', @configuration_group_id, 0, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Language', 'PEPPERJAM_LANGUAGE', 'English', 'The language of the products you wish to include (must match your database).', @configuration_group_id, 0, NOW(), NULL, NULL),
(NULL, 'Language Display', 'PEPPERJAM_LANGUAGE_DISPLAY', 'false', 'Do you want to include the language in your links? (required if your store is non-English default)', @configuration_group_id, 0, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Currency', 'PEPPERJAM_CURRENCY', 'USD', 'The currency of the products you wish to include (must be USD).', @configuration_group_id, 0, NOW(), NULL, NULL),
(NULL, 'Currency Display', 'PEPPERJAM_CURRENCY DISPLAY', 'false', 'Do you want to include the currency in your links? (required if your store is non-USD default)', @configuration_group_id, 0, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),

(NULL, 'Included Categories', 'PEPPERJAM_POS_CATEGORIES', '', 'Enter category ids separated by commas <br>(i.e. 1,2,3)<br>Leave blank to allow all categories', @configuration_group_id, 1, NOW(), NULL, NULL),
(NULL, 'Excluded Categories', 'PEPPERJAM_NEG_CATEGORIES', '', 'Enter category ids separated by commas <br>(i.e. 1,2,3)<br>Leave blank to deactivate', @configuration_group_id, 1, NOW(), NULL, NULL),
(NULL, 'Starting Point', 'PEPPERJAM_START_PRODUCTS', '0', 'Default = 0 (not product_id)', @configuration_group_id, 2, NOW(), NULL, NULL),
(NULL, 'Max products', 'PEPPERJAM_MAX_PRODUCTS', '0', 'Default = 0 for infinite # of products', @configuration_group_id, 2, NOW(), NULL, NULL),

(NULL, 'Quantity', 'PEPPERJAM_QUANTITY', 'false', 'Include the quantity in stock?', @configuration_group_id, 7, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),

(NULL, 'Magic SEO URLs', 'PEPPERJAM_MAGIC_SEO_URLS', 'false', 'Is Magic SEO URLs installed?', @configuration_group_id, 7, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),');