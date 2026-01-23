CREATE TABLE profissionais(
    id_profissional int not null auto_increment primary key,
    nome_profissional varchar(200) not null unique,
    email varchar(255),
    senha varchar(255),
    cargo_profissional varchar(255),
    vinculo varchar(255),
    cidade varchar(255),
    endereco varchar(255),
    cpf int not null unique,
    data_nascimento date,
    status tinyint(1) DEFAULT 1
);

CREATE TABLE usuarios(
    id_usuario int not null auto_increment primary key,
    nome_usuario varchar(200) not null unique,
    numero_prontuario varchar(200) not null unique,
    laudado enum('sim','não'),
    contato_usuario varchar(255),
    situacao varchar(255),
    quantidade_terapias int,
    multiprofissionais varchar(255),
    diagnostico varchar(255),
    informacao_adicional varchar(255),
    status tinyint(1) DEFAULT 1
);

CREATE TABLE agendamentos(
    id_agendamento int not null auto_increment primary key,
    nome_usuario varchar(255) not null,
    nome_profissional varchar(255) not null,
    data date not null,
    hora time not null,
    status_agendamento varchar(255),
    status tinyint(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS usuario_profissional (
id_usuario INT,
id_profissional INT,
PRIMARY KEY (id_usuario, id_profissional),
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
FOREIGN KEY (id_profissional) REFERENCES profissionais(id_profissional)
);

CREATE TABLE agendamento_profissional(
id_agendamento int,
id_profissional int,
primary key(id_agendamento, id_profissional),
foreign key (id_agendamento) references agendamentos(id_agendamento),
foreign key (id_profissional) references profissionais(id_profissional));