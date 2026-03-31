INSERT INTO users (full_name, email, password_hash, role)
VALUES (
	'Default Admin',
	'admin@example.com',
	crypt('change_me', gen_salt('bf')),
	'admin'
)
ON CONFLICT (email) DO NOTHING;

INSERT INTO categories (name, slug)
VALUES
	('Politique', 'politique'),
	('Economie', 'economie'),
	('Diplomatie', 'diplomatie'),
	('Securite', 'securite'),
	('Societe', 'societe')
ON CONFLICT (slug) DO NOTHING;

INSERT INTO articles (
	category_id,
	author_id,
	title,
	slug,
	excerpt,
	content,
	meta_title,
	meta_description,
	is_published,
	published_at,
	updated_at
)
SELECT c.id, u.id,
	data.title,
	data.slug,
	data.excerpt,
	data.content,
	data.meta_title,
	data.meta_description,
	TRUE,
	data.published_at,
	data.updated_at
FROM (
	VALUES
		(
			'politique',
			'Iran renforce son dispositif interieur face au risque de contestation',
			'iran-renforce-dispositif-interieur-risque-contestation',
			'Les autorites iraniennes multiplient arrestations et controles dans un contexte de forte tension interne.',
			'Selon des publications Reuters du 30 mars 2026, les autorites iraniennes ont intensifie leur presence de securite dans plusieurs villes pour prevenir des mouvements de contestation. Les mesures observees incluent davantage de checkpoints, des interpellations ciblees et une pression accrue sur les voix dissidentes. Cette dynamique intervient sur fond de conflit regional et de fragilite economique.',
			'Iran: dispositif interieur renforce face aux tensions',
			'Point de situation sur le renforcement du dispositif de securite interieur en Iran en mars 2026.',
			TIMESTAMP '2026-03-30 09:10:00',
			TIMESTAMP '2026-03-30 11:00:00'
		),
		(
			'diplomatie',
			'Washington et Teheran: les propositions de paix restent contestees',
			'washington-teheran-propositions-paix-contestees',
			'Les canaux de discussion se maintiennent, mais les positions publiques restent tres eloignees.',
			'Des depeches Reuters du 30 mars 2026 signalent une nouvelle sequence de declarations contradictoires entre Washington et Teheran. Les Etats-Unis parlent de progres dans les discussions tandis que la partie iranienne juge certaines propositions irrealisables. Le dossier demeure evolutif avec un risque de deterioration rapide si aucun cadre commun n est valide.',
			'Washington-Teheran: des discussions sous tension',
			'Analyse des principaux points de blocage diplomatique entre Washington et Teheran.',
			TIMESTAMP '2026-03-30 12:20:00',
			TIMESTAMP '2026-03-30 12:40:00'
		),
		(
			'economie',
			'Le conflit regional pese sur les perspectives economiques internationales',
			'conflit-regional-pese-perspectives-economiques-internationales',
			'Le FMI alerte sur les effets d entrainement du conflit sur les pays voisins et les chaines d approvisionnement.',
			'Les institutions internationales evoquent une aggravation des risques macroeconomiques avec une pression sur l energie, le commerce et la confiance des investisseurs. Les informations diffusees le 30 mars 2026 font etat d une degradation des perspectives dans plusieurs economies deja fragilisees. Les scenarios les plus sensibles concernent l inflation importee et les couts de transport.',
			'Conflit Iran: alerte sur les perspectives economiques',
			'Impacts economiques regionaux et mondiaux de la crise autour de l Iran.',
			TIMESTAMP '2026-03-30 13:05:00',
			TIMESTAMP '2026-03-30 13:20:00'
		),
		(
			'economie',
			'Choc industriel: des installations d aluminium au coeur des tensions',
			'choc-industriel-installations-aluminium-coeur-tensions',
			'Les attaques sur des sites industriels strategiques ravivent le debat sur la securite des approvisionnements.',
			'Plusieurs analyses publiees fin mars 2026 soulignent que les interruptions sur des sites d aluminium du Moyen-Orient peuvent affecter les flux vers les Etats-Unis et d autres acheteurs. Au-dela de l effet prix, les experts mentionnent un risque de perturbation logistique pour les secteurs consommateurs de metaux. Le sujet est suivi de pres par les industriels et les places financieres.',
			'Installations d aluminium: impact sur la chaine mondiale',
			'Lecture economique des perturbations sur la filiere aluminium en mars 2026.',
			TIMESTAMP '2026-03-30 14:15:00',
			TIMESTAMP '2026-03-30 15:05:00'
		),
		(
			'securite',
			'Presence militaire accrue au Moyen-Orient: nouvel equilibre de dissuasion',
			'presence-militaire-accrue-moyen-orient-nouvel-equilibre-dissuasion',
			'De nouveaux deploiements renforcent la posture de dissuasion dans la region.',
			'Des depeches de la journee du 30 mars 2026 indiquent l arrivee de nouveaux effectifs et une intensification de la coordination militaire de plusieurs acteurs. Cette configuration vise a contenir une extension du conflit et a proteger des infrastructures sensibles. Les observateurs soulignent toutefois que tout incident local peut provoquer une escalade rapide.',
			'Moyen-Orient: deploiements et dissuasion en hausse',
			'Synthese des mouvements militaires observes autour de l Iran et de leurs implications.',
			TIMESTAMP '2026-03-30 16:00:00',
			TIMESTAMP '2026-03-30 16:25:00'
		),
		(
			'societe',
			'Pression sociale et gestion de crise: le quotidien sous surveillance renforcee',
			'pression-sociale-gestion-crise-quotidien-surveillance-renforcee',
			'Les restrictions de circulation et la multiplication des controles transforment la vie urbaine.',
			'Dans plusieurs centres urbains, la population fait face a un durcissement des controles et a une incertitude economique persistante. Les informations recoupees dans la presse internationale de mars 2026 decrivent un climat de vigilance permanent, avec effets directs sur l activite commerciale et la mobilite. Les ONG rappellent la necessite de proteger les civils et l acces aux services essentiels.',
			'Iran: impacts sociaux d une securite renforcee',
			'Comment les mesures de crise affectent le quotidien des habitants en 2026.',
			TIMESTAMP '2026-03-30 17:10:00',
			TIMESTAMP '2026-03-30 17:50:00'
		)
) AS data(category_slug, title, slug, excerpt, content, meta_title, meta_description, published_at, updated_at)
INNER JOIN categories c ON c.slug = data.category_slug
INNER JOIN users u ON u.email = 'admin@example.com'
ON CONFLICT (slug) DO NOTHING;

-- Seed uploaded files committed in storage/uploads with high priority.
INSERT INTO article_images (article_id, file_path, alt_text, sort_order)
SELECT a.id, data.file_path, data.alt_text, data.sort_order
FROM (
	VALUES
		(
			'iran-renforce-dispositif-interieur-risque-contestation',
			'articles/seed/og-default.svg',
			'Iran renforce son dispositif interieur face au risque de contestation',
			0
		)
) AS data(slug, file_path, alt_text, sort_order)
INNER JOIN articles a ON a.slug = data.slug
AND NOT EXISTS (
	SELECT 1
	FROM article_images ai
	WHERE ai.article_id = a.id
	AND ai.file_path = data.file_path
);

INSERT INTO article_images (article_id, file_path, alt_text, sort_order)
SELECT a.id, 'articles/seed/og-default.svg', a.title, 1
FROM articles a
WHERE a.slug IN (
	'iran-renforce-dispositif-interieur-risque-contestation',
	'washington-teheran-propositions-paix-contestees',
	'conflit-regional-pese-perspectives-economiques-internationales',
	'choc-industriel-installations-aluminium-coeur-tensions',
	'presence-militaire-accrue-moyen-orient-nouvel-equilibre-dissuasion',
	'pression-sociale-gestion-crise-quotidien-surveillance-renforcee'
)
AND NOT EXISTS (
	SELECT 1
	FROM article_images ai
	WHERE ai.article_id = a.id
);
