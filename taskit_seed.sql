
INSERT INTO default_view VALUES
	(1, 1, 1, 'Activity', '{"layout":"TwoColumn", "template": {"head":[{"widget":"QuickTask", "params":{"title":"Create Task"}}],"col0":[{"widget":"TaskList","params":{"title":"My Tasks"}},{"widget":"TaskList","params":{"title":"Tasks I Authored","mode":"createdBy"}}],"col1":[{"widget":"RecentComments","params":{"title":"Recent Comments"}},{"widget":"TaskList","params":{"title":"Unassigned Tasks","mode":"unassigned"}}],"foot":[]}}'),
	(2, 3, 1, 'Open Tasks', '{"layout":"TwoColumn", "template": {"head":[{"widget":"QuickTask","params":{"title":"Create Task"}},{"widget":"TaskList","params":{"title":"Tasks","mode":"all","open":true,"cols":["cplx","name","prio","status","type","author","assign","created"]}}],"col0":[],"col1":[],"foot":[]}}'),
	(3, 3, 2, 'Closed Tasks', '{"layout":"TwoColumn", "template": {"head":[{"widget":"TaskList","params":{"title":"Tasks","mode":"all","open":false,"cols":["cplx","name","prio","status","type","author","assign","created"]}}],"col0":[],"col1":[],"foot":[]}}');

SELECT pg_catalog.setval('default_view_viewid_seq', 3, true);

SELECT reset_all_user_views();

INSERT INTO auth_item VALUES
	-- Operations
	('manageUsers', 0, 'Manage User Accounts', NULL, 'N;'),

	-- Tasks

	-- Roles
	('user', 2, '', NULL, 'N;'),
	('admin', 2, '', NULL, 'N;');

INSERT INTO auth_item_child VALUES
	('admin', 'manageUsers');

