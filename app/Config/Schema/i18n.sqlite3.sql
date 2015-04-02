-- $Id$
--
-- Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)--
-- Licensed under The MIT License
-- For full copyright and license information, please see the LICENSE.txt
-- Redistributions of files must retain the above copyright notice.
-- MIT License (http://www.opensource.org/licenses/mit-license.php)
BEGIN;
DROP TABLE IF EXISTS i18n;
CREATE TABLE i18n (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	locale varchar(6) NOT NULL,
	model varchar(255) NOT NULL,
	foreign_key int(10) NOT NULL,
	field varchar(255) NOT NULL,
	content mediumtext
--	UNIQUE INDEX I18N_LOCALE_FIELD(locale, model, foreign_key, field),
--	INDEX I18N_LOCALE_ROW(locale, model, foreign_key),
--	INDEX I18N_LOCALE_MODEL(locale, model),
--	INDEX I18N_FIELD(model, foreign_key, field),
--	INDEX I18N_ROW(model, foreign_key),
--	INDEX locale (locale),
--	INDEX model (model),
--	INDEX row_id (foreign_key),
--	INDEX field (field)
);
CREATE INDEX locale ON i18n (locale);
CREATE INDEX model ON i18n (model);
CREATE INDEX row_id ON i18n (foreign_key);
CREATE INDEX field ON i18n (field);
COMMIT;