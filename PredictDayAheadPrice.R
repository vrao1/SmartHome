install.packages("zoo", repos = "http://cran.r-project.org")
install.packages("hydroGOF", repos = "http://cran.r-project.org")

library(zoo)
library(methods)
library(stats)
library(graphics)
library(timeDate)
library(hydroGOF)
library(PSF)

p <- psf(nottem)
a <- predict(p, n.ahead = 12)
a
