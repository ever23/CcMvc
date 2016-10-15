SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `escuela` DEFAULT CHARACTER SET utf8 ;
USE `escuela` ;

-- -----------------------------------------------------
-- Table `escuela`.`representante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escuela`.`representante` (
  `ci_repr` VARCHAR(10) NOT NULL,
  `nomb_repr` VARCHAR(30) NOT NULL,
  `apel_repr` VARCHAR(30) NOT NULL,
  `telf_repr` VARCHAR(12) NOT NULL,
  `ocup_repr` VARCHAR(30) NOT NULL,
  `dire_repr` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`ci_repr`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `escuela`.`estudiante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escuela`.`estudiante` (
  `id_estu` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `cedu_estu` CHAR(15) NULL DEFAULT NULL,
  `nomb_estu` CHAR(15) NULL DEFAULT NULL,
  `apel_estu` CHAR(15) NULL DEFAULT NULL,
  `fena_estu` DATE NULL DEFAULT NULL,
  `luga_estu` CHAR(30) NULL DEFAULT NULL,
  `grad_estu` CHAR(4) NULL DEFAULT NULL,
  `secc_estu` CHAR(1) NULL DEFAULT NULL,
  `esco_estu` CHAR(11) NULL DEFAULT NULL,
  `codi_cana` VARCHAR(50) NOT NULL,
  `ci_repr` VARCHAR(10) NOT NULL,
  `pare_repr` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id_estu`),
  INDEX `ci_repr` (`ci_repr` ASC),
  INDEX `ci_repr_2` (`ci_repr` ASC),
  CONSTRAINT `estudiante_ibfk_1`
    FOREIGN KEY (`ci_repr`)
    REFERENCES `escuela`.`representante` (`ci_repr`)
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `escuela`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `escuela`.`user` (
  `nomb_user` VARCHAR(30) NOT NULL,
  `pass_user` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`nomb_user`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
