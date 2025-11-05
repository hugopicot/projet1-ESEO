-- Migration minimale pour ajouter les colonnes d’abonnement manquantes
-- Exécuter sur la base `projet_site1`

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `subscription_plan` VARCHAR(50) DEFAULT 'none',
  ADD COLUMN IF NOT EXISTS `subscription_start` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `subscription_end` DATE DEFAULT NULL;

-- Optionnel: initialiser les anciens utilisateurs sans abonnement explicite
-- UPDATE `users` SET `subscription_plan` = 'none' WHERE `subscription_plan` IS NULL;