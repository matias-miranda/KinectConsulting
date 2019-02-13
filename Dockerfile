ARG DRUPAL_VERSION=8.6.7

#Release Stage
FROM drupal:${DRUPAL_VERSION} as drupal

ARG DISABLE_ENV_VARS

COPY /web/modules /var/www/html/modules
COPY /web/profiles /var/www/html/profiles
COPY /web/themes /var/www/html/themes
#COPY /web/core /var/www/html/core

#Copy patched core files
COPY /patched/MonthDate.php /var/www/html/core/modules/views/src/Plugin/views/argument/MonthDate.php

#Allow environment variables
COPY /web/sites/default/settings.environment.php /var/www/html/sites/default/

RUN if [ -z "${DISABLE_ENV_VARS}" ] ; then echo "Enabled environment variables support." \
    && cp /var/www/html/sites/default/default.settings.php /var/www/html/sites/default/settings.php \
    && chmod 0777 /var/www/html/sites/default/settings.php \
    && printf "if (file_exists(\$app_root . '/' . \$site_path . '/settings.environment.php')) { \n\
   include \$app_root . '/' . \$site_path . '/settings.environment.php'; \n\
 }\n" >> /var/www/html/sites/default/settings.php ; else echo "Disabled environment variables support." ; fi

