FROM postgres:11

# Install requirements
RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		postgis \
		postgresql-11-postgis-2.5 \
        postgresql-11-postgis-2.5-scripts \
	&& rm -rf /var/lib/apt/lists/*

# Optional database initialization
COPY ./init.sql /docker-entrypoint-initdb.d/init.sql
CMD ["docker-entrypoint.sh", "postgres"]