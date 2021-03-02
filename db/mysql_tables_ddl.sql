create table User
(
    id int primary key auto_increment,
    login varchar(50) not null,
    password varchar(50) not null,

    constraint constraintUniqueLogin unique (login)
);

create table LearningStage
(
    id int primary key auto_increment,
    name varchar(50) not null,
    description varchar(50) null,

    constraint constraintUniqueName unique (name)
);

create table Question
(
    id int primary key auto_increment,
    text varchar(255) not null,
    learningStageID int not null,

    foreign key (learningStageID) references LearningStage(id),
    constraint constraintUniqueTextAndLearningStageID unique (text, learningStageID)
);

create table Answer
(
    id int primary key auto_increment,
    text varchar(100) not null,
    mark int not null,
    questionID int not null,

    foreign key (questionID) references Question(id),
    constraint constraintUniqueTextMarkAndQuestionID unique (text, mark, questionID)
);

create table Attempt
(
    id int primary key auto_increment,
    createdAt datetime not null,
    userID int not null,
    learningStageID int not null,

    foreign key (userID) references User(id),
    foreign key (learningStageID) references LearningStage(id),
    constraint constraintUniqueCreatedAtUserIDAndLearningStageID unique (createdAt, userID, learningStageID)
);

create table AttemptField
(
    id int primary key auto_increment,
    attemptID int not null,
    questionID int not null,
    answerID int not null,

    foreign key (attemptID) references Attempt(id),
    foreign key (questionID) references Question(id),
    foreign key (answerID) references Answer(id),
    constraint constraintUniqueAttemptIDQuestionIDAndAnswerID unique (attemptID, questionID, answerID)
);