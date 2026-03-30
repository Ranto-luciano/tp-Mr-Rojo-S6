INSERT INTO users (full_name, email, password_hash, role)
VALUES (
	'Default Admin',
	'admin@example.com',
	crypt('change_me', gen_salt('bf')),
	'admin'
)
ON CONFLICT (email) DO NOTHING;

INSERT INTO categories (name, slug)
VALUES ('General', 'general')
ON CONFLICT (slug) DO NOTHING;

INSERT INTO articles (category_id, author_id, title, slug, excerpt, content, meta_title, meta_description, is_published, published_at)
SELECT c.id, u.id,
	   'Welcome to Iran News',
	   'welcome-to-iran-news',
	   'Initial article created by seed script.',
	   'This is a starter content used to validate routing, rendering and SEO fields.',
	   'Welcome to Iran News',
	   'Starter article for project initialization.',
	   TRUE,
	   NOW()
FROM categories c
CROSS JOIN users u
WHERE c.slug = 'general' AND u.email = 'admin@example.com'
ON CONFLICT (slug) DO NOTHING;
