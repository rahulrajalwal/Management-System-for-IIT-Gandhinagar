-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2021 at 11:01 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: attendancemsystem
--

-- --------------------------------------------------------
--
-- Table structure for table tbladmin
--

CREATE TABLE tbladmin (
  Id int(10) NOT NULL,
  firstName varchar(50) NOT NULL,
  lastName varchar(50) NOT NULL,
  emailAddress varchar(50) NOT NULL,
  password varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tbladmin
--

INSERT INTO tbladmin (Id, firstName, lastName, emailAddress, password) VALUES
(1, 'Admin', 'Liam', 'admin@mail.com', 'D00F5D5217896FB7FD601412CB890830');

-- --------------------------------------------------------

--
-- Table structure for table tblattendance
--

CREATE TABLE tblattendance (
  Id int(10) NOT NULL,
  aadharNumber varchar(255) NOT NULL,
  classId varchar(10) NOT NULL,
  classArmId varchar(10) NOT NULL,
  sessionTermId varchar(10) NOT NULL,
  status varchar(10) NOT NULL,
  dateTimeTaken varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tblattendance
--

INSERT INTO tblattendance (Id, aadharNumber, classId, classArmId, sessionTermId, status, dateTimeTaken) VALUES
(1, '987654321012', '1', '2', '1', '1', '2020-11-01'),
(2, '567890123456', '1', '2', '1', '1', '2020-11-01'),
(3, '345678901234', '1', '2', '1', '1', '2020-11-01'),
(4, '789012345678', '1', '4', '1', '1', '2020-11-01'),
(5, '567890123459', '1', '4', '1', '0', '2020-11-01'),
(6, '147258369025', '1', '4', '1', '1', '2020-11-01'),
(7, '258963475109', '1', '2', '1', '1', '2020-11-19'),
(8, '369147258369', '1', '2', '1', '1', '2020-11-19'),
(9, '472583690123', '1', '2', '1', '1', '2020-11-19'),
(10, '583690123456', '1', '4', '1', '0', '2021-07-15');


-- --------------------------------------------------------

--
-- Table structure for table tblclass
--

CREATE TABLE tblclass (
  Id int(10) NOT NULL,
  className varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tblclass
--

INSERT INTO tblclass (Id, className) VALUES
(1, 'Seven'),
(3, 'Eight'),
(4, 'Nine');

-- --------------------------------------------------------

--
-- Table structure for table tblclassarms
--

CREATE TABLE tblclassarms (
  Id int(10) NOT NULL,
  classId varchar(10) NOT NULL,
  classArmName varchar(255) NOT NULL,
  isAssigned varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tblclassarms
--

INSERT INTO tblclassarms (Id, classId, classArmName, isAssigned) VALUES
(2, '1', 'S1', '1'),
(4, '1', 'S2', '1'),
(5, '3', 'E1', '1'),
(6, '4', 'N1', '1');

-- --------------------------------------------------------

--
-- Table structure for table tblclassteacher
--

CREATE TABLE tblclassteacher (
  Id int(10) NOT NULL,
  firstName varchar(255) NOT NULL,
  lastName varchar(255) NOT NULL,
  emailAddress varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  phoneNo varchar(50) NOT NULL,
  classId varchar(10) NOT NULL,
  classArmId varchar(10) NOT NULL,
  dateCreated varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tblclassteacher
--

INSERT INTO tblclassteacher (Id, firstName, lastName, emailAddress, password, phoneNo, classId, classArmId, dateCreated) VALUES
(1, 'Will', 'Williams', 'teacher@mail.com', '32250170a0dca92d53ec9624f336ca24', '09089898999', '1', '2', '2020-10-31'),
(4, 'Demola', 'Ade', 'Kumolu@gmail.com', '32250170a0dca92d53ec9624f336ca24', '09672002882', '1', '4', '2020-11-01'),
(5, 'Ryan', 'McQuie', 'ryan@mail.com', '32250170a0dca92d53ec9624f336ca24', '7014560000', '3', '5', '2021-10-07'),
(6, 'John', 'Greenwood', 'jwood@mail.com', '32250170a0dca92d53ec9624f336ca24', '0100000030', '4', '6', '2021-10-07');

-- --------------------------------------------------------

--
-- Table structure for table tblsessionterm
--

CREATE TABLE tblsessionterm (
  Id int(10) NOT NULL,
  sessionName varchar(50) NOT NULL,
  termId varchar(50) NOT NULL,
  isActive varchar(10) NOT NULL,
  dateCreated varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tblsessionterm
--

INSERT INTO tblsessionterm (Id, sessionName, termId, isActive, dateCreated) VALUES
(1, '2019/2020', '1', '1', '2020-10-31'),
(3, '2019/2020', '2', '0', '2020-10-31');

-- --------------------------------------------------------

--
-- Table structure for table tblstudents

CREATE TABLE tblstudents (
  Id int(10) NOT NULL,
  aadharNumber varchar(20) NOT NULL,
  surname varchar(50) NOT NULL,
  name varchar(50) NOT NULL,
  secondName varchar(50) DEFAULT NULL,
  mobile varchar(10) NOT NULL,
  alternateMobile varchar(10) DEFAULT NULL,
  email varchar(50) NOT NULL,
  currentAddress text NOT NULL,
  village varchar(50) NOT NULL,
  permanentAddress text NOT NULL,
  age int(3) NOT NULL,
  dob date NOT NULL,
  gender enum('Male','Female','Other') NOT NULL,
  education varchar(255) NOT NULL,
  currentStatus enum('Searching for Job','Doing Job','Studying','Business') NOT NULL,
  familyWorkingStatus text NOT NULL,
  workExperience text,
  livePhoto varchar(255) NOT NULL,
  idFront varchar(255) NOT NULL,
  idBack varchar(255) NOT NULL,
  deposited enum('Deposit Received','ITI Student') NOT NULL,
  classId int(10) NOT NULL,
  classArmId int(10) NOT NULL,
  dateCreated datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table tblstudents

INSERT INTO tblstudents (Id, aadharNumber, surname, name, secondName, mobile, alternateMobile, email, currentAddress, village, permanentAddress, age, dob, gender, education, currentStatus, familyWorkingStatus, workExperience, livePhoto, idFront, idBack, deposited, classId, classArmId, dateCreated) VALUES
(1, '987654321012', 'Smith', 'John', 'William', '1234567890', '987654321', 'john@mail.com', '456 Main St', 'Village 2', '456 Permanent St', 22, '2000-03-20', 'Male', 'High School Diploma', 'Studying', 'Most family members are working', 'Internship at a local company', 'student1_live_photo.jpg', 'student1_id_front.jpg', 'student1_id_back.jpg', 'Deposit Received', 1, 2, '2021-10-07 11:01:00'),
(2, '567890123456', 'Williams', 'Emma', 'Brown', '9876543211', '111222333', 'emma@mail.com', '789 Elm St', 'Village 3', '789 Elm St', 20, '2001-08-14', 'Female', 'High School Student', 'Studying', 'Family members work in different fields', 'No work experience yet', 'student2_live_photo.jpg', 'student2_id_front.jpg', 'student2_id_back.jpg', 'Deposit Received', 1, 4, '2021-10-07 11:01:00'),
(3, '345678901234', 'Brown', 'David', 'Smith', '9876543212', '222333444', 'david@mail.com', '123 Oak St', 'Village 4', '123 Oak St', 21, '2000-12-05', 'Male', 'High School Diploma', 'Searching for Job', 'Family has diverse working backgrounds', 'Interned at a tech startup', 'student3_live_photo.jpg', 'student3_id_front.jpg', 'student3_id_back.jpg', 'Deposit Received', 3, 5, '2021-10-07 11:01:00'),
(4, '789012345678', 'Miller', 'Olivia', 'Taylor', '9876543213', '444555666', 'olivia@mail.com', '567 Pine St', 'Village 5', '567 Pine St', 19, '2002-02-28', 'Female', 'High School Student', 'Studying', 'Family members work in various sectors', 'No work experience so far', 'student4_live_photo.jpg', 'student4_id_front.jpg', 'student4_id_back.jpg', 'Deposit Received', 4, 6, '2021-10-07 11:01:00'),
(5, '567890123459', 'Johnson', 'Benjamin', 'Anderson', '9876543214', '555666777', 'ben@mail.com', '890 Maple St', 'Village 6', '890 Maple St', 23, '1998-09-10', 'Male', 'College Graduate', 'Doing Job', 'Family has a mix of working professionals', 'Worked as a part-time tutor', 'student5_live_photo.jpg', 'student5_id_front.jpg', 'student5_id_back.jpg', 'Deposit Received', 5, 7, '2021-10-07 11:01:00'),
(6, '147258369025', 'Wilson', 'Emily', 'Davis', '9876543215', '666777888', 'emily@mail.com', '234 Birch St', 'Village 7', '234 Birch St', 22, '1999-04-30', 'Female', 'College Student', 'Studying', 'Family members work in education and healthcare', 'Internship at a local nonprofit', 'student6_live_photo.jpg', 'student6_id_front.jpg', 'student6_id_back.jpg', 'Deposit Received', 1, 2, '2021-10-07 11:01:00'),
(7, '258963475109', 'Moore', 'Michael', 'Wilson', '9876543216', '777888999', 'michael@mail.com', '678 Spruce St', 'Village 8', '678 Spruce St', 21, '2000-11-15', 'Male', 'High School Diploma', 'Searching for Job', 'Family has diverse working backgrounds', 'No work experience yet', 'student7_live_photo.jpg', 'student7_id_front.jpg', 'student7_id_back.jpg', 'Deposit Received', 3, 4, '2021-10-07 11:01:00'),
(8, '369147258369', 'Taylor', 'Sophia', 'Moore', '9876543217', '888999000', 'sophia@mail.com', '901 Cypress St', 'Village 9', '901 Cypress St', 18, '2003-06-21', 'Female', 'High School Student', 'Studying', 'Family members work in various fields', 'No work experience yet', 'student8_live_photo.jpg', 'student8_id_front.jpg', 'student8_id_back.jpg', 'Deposit Received', 4, 5, '2021-10-07 11:01:00'),
(9, '472583690123', 'Anderson', 'William', 'Miller', '9876543218', '999000111', 'william@mail.com', '321 Poplar St', 'Village 10', '321 Poplar St', 20, '2001-01-02', 'Male', 'High School Student', 'Studying', 'Family members work in education and business', 'Internship at a local business', 'student9_live_photo.jpg', 'student9_id_front.jpg', 'student9_id_back.jpg', 'Deposit Received', 5, 6, '2021-10-07 11:01:00'),
(10, '583690123456', 'Thomas', 'Liam', 'Johnson', '9876543219', '111222333', 'liam@mail.com', '111 Elm St', 'Village 1', '111 Elm St', 19, '2002-09-18', 'Male', 'High School Student', 'Studying', 'Family members work in healthcare and IT', 'No work experience yet', 'student10_live_photo.jpg', 'student10_id_front.jpg', 'student10_id_back.jpg', 'Deposit Received', 1, 7, '2021-10-07 11:01:00');


-- --------------------------------------------------------

--
-- Table structure for table tblterm
--

CREATE TABLE tblterm (
  Id int(10) NOT NULL,
  termName varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table tblterm
--

INSERT INTO tblterm (Id, termName) VALUES
(1, 'First'),
(2, 'Second'),
(3, 'Third');

--
-- Indexes for dumped tables
--

--
-- Indexes for table tbladmin
--
ALTER TABLE tbladmin
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblattendance
--
ALTER TABLE tblattendance
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblclass
--
ALTER TABLE tblclass
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblclassarms
--
ALTER TABLE tblclassarms
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblclassteacher
--
ALTER TABLE tblclassteacher
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblsessionterm
--
ALTER TABLE tblsessionterm
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblstudents
--
ALTER TABLE tblstudents
  ADD PRIMARY KEY (Id);

--
-- Indexes for table tblterm
--
ALTER TABLE tblterm
  ADD PRIMARY KEY (Id);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table tbladmin
--
ALTER TABLE tbladmin
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table tblattendance
--
ALTER TABLE tblattendance
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table tblclass
--
ALTER TABLE tblclass
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table tblclassarms
--
ALTER TABLE tblclassarms
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table tblclassteacher
--
ALTER TABLE tblclassteacher
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table tblsessionterm
--
ALTER TABLE tblsessionterm
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table tblstudents
--
ALTER TABLE tblstudents
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table tblterm
--
ALTER TABLE tblterm
  MODIFY Id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;